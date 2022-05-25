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
}
