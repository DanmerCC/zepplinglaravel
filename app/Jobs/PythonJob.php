<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PythonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $myzip = new \ZipArchive();
        $path = "";
        $filename = "";
        $toUnzipPath = "";

        if ($myzip->open($path . "/$filename.zip", \ZipArchive::RDONLY) === TRUE) {
            $myzip->extractTo($toUnzipPath);
            $myzip->close();
        }
    }
}
