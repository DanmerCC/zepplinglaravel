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
        return $this->sendResponse(CustomSearch::paginate(), "Listado correctamente");
    }
    function new(NewCustomSearchRequest $request)
    {
        $newModel = new CustomSearch($request->only(['day', 'hour']));
        $newModel->save();

        dispatch(new RunNewCustomSearchJob($newModel));
        return $this->sendResponse($newModel, "Tarea agregada");
    }
}
