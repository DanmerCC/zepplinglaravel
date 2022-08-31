<?php

namespace App\Http\Controllers;

use App\Core\State;
use App\Core\States;
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
        $last = CustomSearch::orderBy('id', 'DESC')->first();
        if ($last != null) {
            if ($last->state != States::FAILED && $last->state != States::ENDED) {

                return $this->sendError("Ya hay otro proceso en curso", 503);
            }
        }
        $newModel = new CustomSearch($request->only(['day', 'hour', 'ip_publica']));
        $newModel->state = 'STARTED';
        $newModel->save();

        dispatch(new RunNewCustomSearchJob($newModel));

        return $this->sendResponse($newModel, "Tarea agregada");
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
