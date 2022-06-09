<?php

namespace App\Jobs;

use App\Models\Process;
use App\Models\Result;
use App\ResponseZeppelin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParagraphJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fecha;
    private $process;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fecha,Process $process)
    {
        $this->fecha  = $fecha;
        $this->process  = $process;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = null;
        try {
            $currentProcess = Process::find($this->process->id);

            if($currentProcess->status == 'ENDED') return;
            $noteBookId = env('ZEPLLING_BOOK1_ID');
            $paragraphForDescompressId = env('ZEPLLING_PARAGRAPHO_DESCOMPRESS_IDL');
            Log::infO("Iniciando proceso de descompresion ".env('ZEPLLING_HOST'));
            $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
            $result = $obj->paragraph()->runParagraphSync(str_replace('paragraph_','',$noteBookId),$paragraphForDescompressId,[
                    "params"=>[
                        "fecha"=>$this->fecha
                    ]
            ]);
            Log::info("Resultado de api:");
            Log::info((array) ($result));
            Log::info(ResponseZeppelin::validResponse((array)$result));
            Log::info(ResponseZeppelin::getDataResponse((array)$result));

            $this->process->out_descompress = json_encode($result);
            $this->process->status = 'ENDED';
            $this->process->save();


        } catch (\Throwable $th) {
            Log::info("error al ejecutar paragrafo");
            $this->process->out_descompress = $th->getMessage();
            $this->process->status = 'FAIL';
            $this->process->save();
            report($th);
        }

        $history = new Result();
        $history->notebook_id = $noteBookId;
        $history->paragraph_id = $paragraphForDescompressId;
        $history->outout = ResponseZeppelin::getDataResponse((array)$result);
        $history->response = json_decode($result);
        $history->process_id = $this->process->id;
        $history->save();

    }
}
