<?php

namespace App\Jobs;

use App\Core\States;
use App\Models\CustomSearch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RunNewCustomSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;

    protected $descompress = "0";

    protected $executor;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CustomSearch $newQuery,User $user = null,bool $descompress = false)
    {

        $this->search = $newQuery;
        $this->executor= $user;
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
        $date = Carbon::now()->format('Y-m-d H:i:s');
        //$command = "python3 ../pythonstore/prueba.py " . str_replace("-", "_", $this->search->day->format('Y-m-d')) . ' > /' . $this->search->id . '.log 2>&1 & echo $!; ';
        $command = "python3 ".env('SCRIPT_PATH')." ".$this->descompress." ". str_replace("-", "_", $this->search->day->format('Y-m-d'))." ".$this->search->hour." ".$this->search->ip_publica ;

        Log::info("Ejecutando: ".$command);
        exec($command, $output);

        $this->search->output = implode(",", $output);
        $this->search->state = States::ENDED;
        $this->search->save();
        if($this->executor !=null){
            $email = $this->executor->email;
            Mail::raw('Hi, welcome user!', function ($message)use($date,$email) {
                $message->to($email)->subject("Ejecucion $date terminada");
            });
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        report($exception);
        $this->search->state = States::FAILED;
        $this->search->save();
    }
}
