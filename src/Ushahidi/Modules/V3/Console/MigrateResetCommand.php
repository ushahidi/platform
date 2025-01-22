<?php

namespace Ushahidi\Modules\V3\Console;

use Illuminate\Console\Command;

class MigrateResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phinx:migrate:reset';

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
        $this->call('phinx:migrate:rollback', [
            '--target' => 0,
        ]);
    }
}
