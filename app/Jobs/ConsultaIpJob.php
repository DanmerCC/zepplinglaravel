<?php

namespace App\Jobs;

use App\Interfaces\IProcessor;
use App\Models\Process;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConsultaIpJob implements ShouldQueue,IProcessor
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $process;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Process $process,$ip)
    {
        $this->process = $process;
        $this->process->ip = $ip;
    }

    public function getName(): string
    {
        return 'consulta_ip';
    }

    public function getPathOutput():string
    {
        return storage_path('app/output/'.$this->process->id.'/'.$this->getName()."/");

    }
    public function getParamsRules():array
    {
        return [
            'ip'=>'required|ip'
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        exec("sudo python3 ./pythonstore/prueba.py {$this->process->date_descompress} $this->", $output, $return_var);
        $this->process->out_descompress = json_encode($output);
        $this->process->status = 'ENDED';
        $this->process->save();
    }
}
