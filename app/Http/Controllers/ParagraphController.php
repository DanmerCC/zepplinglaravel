<?php

namespace App\Http\Controllers;

use App\Http\Requests\DescromprimirRequest;
use App\Http\Requests\RunParagrafoRequest;
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
        $process->status = 'STARTED';
        $process->save();
        $fecha = $request->get('fecha');
        dispatch(new ParagraphJob($fecha,$process));

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


    function process(){
        return Process::query()->orderBy('id', 'DESC')->get();
    }
}
