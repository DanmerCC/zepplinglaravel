<?php

namespace App\Http\Controllers;

use App\Http\Requests\DescromprimirRequest;
use App\Jobs\ParagraphJob;
use App\Models\PharagraphResult;
use App\Models\Process;
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

    function paragrafo($index,Request $request){

        $params = $request->get('params')??[];

        $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
        $result = $obj->paragraph()->runParagraphSync(str_replace('paragraph_','',env('ZEPLLING_BOOK1_ID')),$index,[
                "params"=>$params
        ]);

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
