<?php

namespace App\Jobs;

use App\Models\Process;
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
        try {

            Log::infO("Iniciando proceso de descompresion ".env('ZEPLLING_HOST'));
            $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
            $result = $obj->paragraph()->runParagraphSync(str_replace('paragraph_','',env('ZEPLLING_BOOK1_ID')),env('ZEPLLING_PARAGRAPHO_DESCOMPRESS_IDL'),[
                    "params"=>[
                        "fecha"=>$this->fecha
                    ]
            ]);
            Log::info("url desconocida");
            Log::info((array) ($result));
            $this->process->out_descompress = json_encode($result);
            $this->process->status = 'ENDED';
            $this->process->save();
        } catch (\Throwable $th) {
            $this->process->out_descompress = $th->getMessage();
            $this->process->status = 'FAIL';
            $this->process->save();
        }

    }
}
