<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;

class MigrateResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all database migrations.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('migrate:rollback', [
            '--target' => 0
        ]);
    }
}
