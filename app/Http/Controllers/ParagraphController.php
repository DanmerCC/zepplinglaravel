<?php

namespace App\Http\Controllers;

use App\Http\Requests\DescromprimirRequest;
use App\Http\Requests\RunParagrafoRequest;
use App\Jobs\DescromprimirJob;
use App\Jobs\ParagraphJob;
use App\Models\PharagraphResult;
use App\Models\Process;
use App\Models\Result;
use App\ResponseZeppelin;
use Illuminate\Http\Request;

class ParagraphController extends Controller
{
    function descomprimir(DescromprimirRequest $request){

        $process = new Process();
        $process->date_descompress = $request->fecha;
        $process->save();

        dispatch(new DescromprimirJob($process));

        return "trabajo agregado";
    }

    function paragrafo($index,RunParagrafoRequest $request){

        $noteBookId = env('ZEPLLING_BOOK1_ID');
        $paragraphForDescompressId = $index;

        $params = $request->get('params')??[];

        $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
        $result = $obj->paragraph()->runParagraphSync(str_replace('paragraph_','',env('ZEPLLING_BOOK1_ID')),$index,[
                "params"=>$params
        ]);

        $resultModel = new Result();
        $resultModel->notebook_id = $noteBookId;
        $resultModel->paragraph_id = $paragraphForDescompressId;
        $resultModel->outout = json_encode(ResponseZeppelin::getDataResponse((array)$result));
        $resultModel->response = json_encode($result);
        $resultModel->process_id = $request->process_id;
        $resultModel->save();

        return $result;
    }

    function paragraphs(){

        $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
        $result =  $obj->note()->one(env('ZEPLLING_BOOK1_ID'));

        unset($result->body->paragraphs[0]);
        return $result->body->paragraphs;
    }

    function stopDecompres(Request $request){
        $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
        $result = $obj->paragraph()->stopParagraph(env('ZEPLLING_BOOK1_ID'), env('ZEPLLING_PARAGRAPHO_DESCOMPRESS_IDL'));
        if($result->status == 'OK'){
            $process = Process::find($request->id);
            $process->status = 'ENDED';
            $process->out_descompress = $process->out_descompress.'Cancelado por el usuario';
            $process->save();
        }

        $processNoIncluided = Process::where('status','=','STARTED')->get();

        $processNoIncluided->each(function(Process $item){
            $item->status = 'ENDED';
            $item->out_descompress = $item->out_descompress."\n Cancelado por el usuario";
            $item->save();
            return $item;
        });

        Process::query()->where('status','=','STARTED')->update(['status'=>'ENDED']);
        return $result;
    }

    function process(){
        return Process::query()->orderBy('id', 'DESC')->get();
    }
}
