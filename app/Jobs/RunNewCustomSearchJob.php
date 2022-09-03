<?php

namespace App\Jobs;

use App\Core\States;
use App\Models\CustomSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunNewCustomSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;

    protected $descompress = "0";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CustomSearch $newQuery,bool $descompress = false)
    {

        $this->search = $newQuery;

        if($descompress){
            $this->descompress = "1";
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$command = "python3 ../pythonstore/prueba.py " . str_replace("-", "_", $this->search->day->format('Y-m-d')) . ' > /' . $this->search->id . '.log 2>&1 & echo $!; ';
        $command = "python3 ".env('SCRIPT_PATH')." ".$this->descompress." ". str_replace("-", "_", $this->search->day->format('Y-m-d')." ".$this->search->hora." ".$this->search->ip_publica) ;
        exec($command, $output);

        $this->search->output = implode(",", $output);
        $this->search->state = States::ENDED;
        $this->search->save();
    }
}
