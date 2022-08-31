<?php

namespace App\Http\Controllers;

use App\Core\State;
use App\Core\States;
use App\Http\Requests\NewCustomSearchRequest;
use App\Jobs\RunNewCustomSearchJob;
use App\Models\CustomSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomSearchController extends Controller
{
    function index()
    {
        return $this->sendResponse(CustomSearch::query()->orderBy('id', 'DESC')->paginate(), "Listado correctamente");
    }


    function columnsInfo(){
        return [
            "Fecha"=>"2022-06-11",
            "Hora"=>"11:",
            "Min"=>"00:06",
            "SourceIP"=>"100.104.22.252",
            "SourcePort"=>"36600",
            "SourceNatIP"=>"179.19.218.238",
            "SourceNatPort"=>"3458",
            "DestinationIP"=>"157.240.6.52",
            "DestinationPort"=>"443",
            "Protocol"=>"tcp."
        ];
    }

    function detailIndex(Request $request)
    {
        /**select  cgnat.Hora as cgnat_hora ,cgnat.Min as cgnat_minunto,cem.* from prueba.df_cgnat_SourceNatIP_Dest_file cgnat join prueba.df_joined cem on cgnat.SourceIP = cem.SourceIP where cem.nombre_cliente like "%Salazar Quiceno" and cgnat.Hora='11:';*/
        $cgnatTable = "df_cgnat_SourceNatIP_Dest_file";
        $userdata = "df_joined";
        $queryBase = DB::connection('mysql_dfs')
        ->table($cgnatTable)
        ->select('cgnat.Hora','cgnat.Min',$userdata.'.*')
        ->join($userdata,$cgnatTable.".SourceIP","=",$userdata.".SourceIP","inner")
        ;

        if($request->has('Hora')){
            $queryBase->where("Hora","=",$request->get('Hora').":");
        }
        if($request->has('Min')){
            $queryBase->where("Min","like",$request->get('Min')."%");
        }

        $cgnat = $queryBase->paginate();
        return $this->sendResponse($cgnat, "Listado correctamente");
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

        $command = "python3 ".env('SCRIPT_PATH')." " . str_replace("-", "_", $newModel->day->format('Y-m-d')) . ' > ' . $newModel->id . '.log 2>&1 & echo $!; ';
        $pid = exec($command, $output);
        //dispatch(new RunNewCustomSearchJob($newModel));
        return $this->sendResponse($pid, "Tarea agregada");
    }
}
