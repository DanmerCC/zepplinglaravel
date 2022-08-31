<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewCustomSearchRequest;
use App\Jobs\RunNewCustomSearchJob;
use App\Models\CustomSearch;
use Illuminate\Http\Request;

class CustomSearchController extends Controller
{
    function index()
    {
        return $this->sendResponse(CustomSearch::query()->orderBy('id', 'DESC')->paginate(), "Listado correctamente");
    }

    function detailIndex()
    {
        return $this->sendResponse(CustomSearch::query()->orderBy('id', 'DESC')->paginate(), "Listado correctamente");
    }
    function new(NewCustomSearchRequest $request)
    {
        $newModel = new CustomSearch($request->only(['day', 'hour']));
        $newModel->save();

        $command = "python3 ../pythonstore/prueba.py " . str_replace("-", "_", $newModel->day->format('Y-m-d')) . ' > ' . $newModel->id . '.log 2>&1 & echo $!; ';
        $pid = exec($command, $output);
        $newModel->pid = $pid;

        return $this->sendResponse($pid, "Tarea agregada");
    }

    function inload(NewCustomSearchRequest $request)
    {
        $newModel = new CustomSearch($request->only(['day', 'hour']));
        $newModel->save();

        $command = "python3 ../pythonstore/prueba.py " . str_replace("-", "_", $newModel->day->format('Y-m-d')) . ' > ' . $newModel->id . '.log 2>&1 & echo $!; ';
        $pid = exec($command, $output);
        //dispatch(new RunNewCustomSearchJob($newModel));
        return $this->sendResponse($pid, "Tarea agregada");
    }
}
