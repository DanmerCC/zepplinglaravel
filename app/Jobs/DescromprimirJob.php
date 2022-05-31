<?php

namespace App\Jobs;

use App\Models\PharagraphResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DescromprimirJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fecha;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::infO("Iniciando proceso de descompresion ".env('ZEPLLING_HOST'));
        $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' =>env('ZEPLLING_HOST')]);

        $parag = new PharagraphResult();
        $parag->paragraph_id = env('ZEPLLING_PARAGRAPHO_IDL');
        $parag->book_id = env('ZEPLLING_BOOK1_ID');
        $parag->status = 'START';
        $parag->save();
        $result = $obj->paragraph()->runParagraphSync($parag->book_id,$parag->paragraph_id,[
                "params"=>[
                    "fecha"=>$this->fecha
                ]
        ]);

        $parag->status = 'ENDED';
        $parag->result = $result;
        $parag->save();
        Log::infO("Terminando proceso de descompresion");

    }
}
