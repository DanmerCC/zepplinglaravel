<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class jobtest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $job = new \App\Jobs\TestJob();
        \dispatch($job);
        return 0;
    }
}
