<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
    public function __construct($fecha,$process)
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
        $obj = new \ZeppelinAPI\Zeppelin(['baseUrl' => env('ZEPLLING_HOST')]);
        $result = $obj->paragraph()->runParagraphSync(env('ZEPLLING_BOOK1_ID'),env('ZEPLLING_PARAGRAPHO_DESCOMPRESS_IDL'),[
                "params"=>[
                    "date"=>"lograste!!"
                ]
        ]);

        $this->process->out_descompress = json_encode($result);
        $this->process->status = 'ENDED';
        $this->process->save();

    }
}
