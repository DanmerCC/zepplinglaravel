<?php

namespace App\Http\Controllers;

use App\Service\EmpirixService;
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
        /**
         *
         */
        /*
        "Hora": "11:",
"Min": "01:32",
"imsi": "732360022727835",
"sequence": "912474090314449154",
"start_time": "2022_06_28 13:24:59",
"end_time": "2022_06_28 13:25:41",
"msisdn": "573142179931",
"imei": "867265035016147",
"lac_tac": "ac1",
"sac_eci": "345105",
"ip_address_assigned": "10.73.234.54",
"country_code": "732",
"network_id": "360",
"msisdn1": "3142179931",
"nombre_cliente": "Hector Dionisio Martinez Malaver",
"Tipo_de_cliente": "Persona Natural",
"Tipo_de_Servicio": "Postpaid",
"Dotacion": "NO",
"Plan": "Plan XS 12_GB/ili_MIN/ili_SMS",
"Ciudad_cliente": "Bucaramanga",
"Genero": "Masculino",
"Ending": "SI",
"Rango_de_edad": "No Definido",
"SourceIP": "10.73.234.54",
"SourceNatIP": "179.19.21.84",
"DestinationIP": "172.217.30.194"
*/
        /**select  cgnat.Hora as cgnat_hora ,cgnat.Min as cgnat_minunto,cem.* from prueba.df_cgnat_SourceNatIP_Dest_file cgnat join prueba.df_joined cem on cgnat.SourceIP = cem.SourceIP where cem.nombre_cliente like "%Salazar Quiceno" and cgnat.Hora='11:';*/
        $cgnatTable = "df_cgnat_SourceNatIP_Dest_file";
        $userdata = "df_joined";
        $queryBase = DB::connection('mysql_dfs')
        ->table($cgnatTable)
        ->select($cgnatTable.'.Hora',$cgnatTable.'.Min',$userdata.'.*')
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
        return $this->sendResponse($pid, "Tarea agregada");
    }

    function getMapUrl(Request $request ){
        $service = new EmpirixService(env('EMPIRIX_USER'), env('EMPIRIX_PASSWORD'));
        $response = $service->getData($request->get("page")??1,$request->get("limit")??10,$request->get("ac"),$request->get("cell"));
        return $this->sendResponse($response,"Correctamente cargado");
    }
}
