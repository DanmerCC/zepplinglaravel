<?php

namespace App\Http\Controllers;

use App\Http\Requests\DescromprimirRequest;
use App\Jobs\ParagraphJob;
use App\Models\PharagraphResult;
use Illuminate\Http\Request;

class ParagraphController extends Controller
{
    function descomprimir(DescromprimirRequest $request){

        $fecha = $request->get('fecha');
        dispatch(new ParagraphJob($fecha));

        return "trabajo agregado";
    }
}
