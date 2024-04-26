<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\TestJob;

class FireCommand extends Command
{
    protected $signature = 'fire';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        TestJob::dispatch();
    }
}
