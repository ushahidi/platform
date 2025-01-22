<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMultisiteJobCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'test:multisitejob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue a TestMultisiteJob.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        dispatch(new \App\Jobs\TestMultisiteJob());
    }
}
