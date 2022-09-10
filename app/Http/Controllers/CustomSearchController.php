<?php

namespace App\Http\Controllers;

use App\Service\EmpirixService;
use App\Core\State;
use App\Core\States;
use App\Http\Requests\NewCustomSearchRequest;
use App\Jobs\RunNewCustomSearchJob;
use App\Models\CustomSearch;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomSearchController extends Controller
{
    function index()
    {
        return $this->sendResponse(CustomSearch::query()->orderBy('id', 'DESC')->paginate(), "Listado correctamente");
    }


    function columnsInfo()
    {
        return [
            "Fecha" => "2022-06-11",
            "Hora" => "11:",
            "Min" => "00:06",
            "SourceIP" => "100.104.22.252",
            "SourcePort" => "36600",
            "SourceNatIP" => "179.19.218.238",
            "SourceNatPort" => "3458",
            "DestinationIP" => "157.240.6.52",
            "DestinationPort" => "443",
            "Protocol" => "tcp."
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
        $filter_minute = $request->get("filter_minute");
        $queryBase = DB::connection('mysql_dfs')
            ->table($cgnatTable)
            ->select($cgnatTable . '.Hora', $cgnatTable . '.Min', $userdata . '.*')
            ->join($userdata, $cgnatTable . ".SourceIP", "=", $userdata . ".SourceIP", "inner");

        if ($filter_minute != null) {
            $json = json_decode($filter_minute);
            /*$minutes = [
                "00",
                "01",
                "02",
                "03",
                "04",
                "05",
                "06",
                "07",
                "08",
                "09",
                "10",
                "11",
                "12",
                "13",
                "14",
                "15",
                "16",
                "17",
                "18",
                "19",
                "20",
                "21",
                "22",
                "23",
                "24",
                "25",
                "26",
                "27",
                "28",
                "29",
                "30",
                "31",
                "32",
                "33",
                "34",
                "35",
                "36",
                "37",
                "38",
                "39",
                "40",
                "41",
                "42",
                "43",
                "44",
                "45",
                "46",
                "47",
                "48",
                "49",
                "50",
                "51",
                "52",
                "53",
                "54",
                "55",
                "56",
                "57",
                "58",
                "59",
                "60",
            ];*/
            $minute_start = (int)$json->start;
            $minute_end = (int)$json->end;

            if ($minute_start > $minute_end) {
                throw new Exception("Invalid range: ");
            }
            $minutes = [];

            for ($i = $minute_start; $i <= $minute_end; $i++) {
                array_push($minutes, $i <= 9 ? "0" . $i : "" . $i);
            }

            if (count($minutes) > 1) {
                $queryBase->where(function ($query) use ($minutes) {
                    for ($i = 0; $i < count($minutes); $i++) {
                        if ($i == 0) {
                            $query->where("Min", "like", $minutes[$i] . "%");
                        } else {
                            $query->orWhere("Min", "like", $minutes[$i] . "%");
                        }
                    }
                });
            }
        }
        if ($request->has('Hora')) {
            $queryBase->where("Hora", "=", $request->get('Hora') . ":");
        }
        if ($request->has('Min')) {
            $queryBase->where("Min", "like", $request->get('Min') . "%");
        }

        $cgnat = $queryBase->paginate();
        return $this->sendResponse($cgnat, "Listado correctamente");
    }
    function new(NewCustomSearchRequest $request)
    {
        $notify = $request->get('email_notify') ?? false;
        $last = CustomSearch::orderBy('id', 'DESC')->first();
        if ($last != null) {
            if ($last->state != States::FAILED && $last->state != States::ENDED) {

                return $this->sendError("Ya hay otro proceso en curso", 503);
            }
        }
        $newModel = new CustomSearch($request->only(['day', 'hour', 'ip_publica']));
        $newModel->state = 'STARTED';
        $newModel->user_id = auth()->user()->id;
        $newModel->save();

        $date = Carbon::now()->format('Y-m-d');

        $command = "python3 ".env('SCRIPT_PATH');
        $command.=" ".($date != $last->day->format('Y-m-d')?"1":"0")." ". str_replace("-", "_", $newModel->day->format('Y-m-d'))." ".$newModel->hour." ".$newModel->ip_publica ;
        $command.=" ".route("handler.endscript",["id"=>$newModel->id])." > /bigdata/scripts/buscador".Carbon::now()->format('Y_m_d_H_i_s').".log 2>&1 &";
        //Log::info($command);
        //exec($command);
        Log::info("Despachando job con ...");
        Log::info("date:");
        Log::info($date);
        Log::info("last date:");
        Log::info($last->day->format('Y-m-d'));
        dispatch(new RunNewCustomSearchJob($newModel, $notify ? auth()->user() : null, $date != $last->day->format('Y-m-d')));

        return $this->sendResponse($newModel, "Tarea agregada");
    }

    function handlerEndScript($id){

        Log::info("Recibiendo evento de terminado de script");
        $custom  = CustomSearch::find($id);
        $custom->state = "ENDED";
        $custom->save();

        Mail::raw('Hi, welcome user!', function ($message)use($custom) {
            $message->to("leandro.riveraact@wom.co")->subject("Ejecucion ".$custom->day->format('Y-m-d')." terminada");
        });

        return $this->sendResponse(null,"Correctamente recibido");
    }

    function inload(NewCustomSearchRequest $request)
    {
        $newModel = new CustomSearch($request->only(['day', 'hour']));
        $newModel->save();

        $command = "python3 " . env('SCRIPT_PATH') . " " . str_replace("-", "_", $newModel->day->format('Y-m-d')) . ' > ' . $newModel->id . '.log 2>&1 & echo $!; ';
        $pid = exec($command, $output);
        return $this->sendResponse($pid, "Tarea agregada");
    }

    function getLastCustomSearch()
    {
        $last = CustomSearch::orderBy('id', 'DESC')->first();
        return $this->sendResponse($last, "Ultima tarea registrada");
    }

    function getMapUrl(Request $request)
    {
        $service = new EmpirixService(env('EMPIRIX_USER'), env('EMPIRIX_PASSWORD'));
        $response = $service->getCoordenadas($request->get("ac"), $request->get("cell"), $request->get("page") ?? 1, $request->get("limit") ?? 10);
        return $this->sendResponse($response, "Correctamente cargado");
    }
}
