<?php

namespace Ushahidi\Modules\V3\Console;

use Illuminate\Console\Command;

class MigrateRefreshCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'phinx:migrate:refresh
        {--t|target=0 : The version number to rollback to}
        {--d|date= : The date to rollback to}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and re-run all migrations.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $options = [
            '--target' => $this->option('target'),
        ];
        if ($this->option('date')) {
            $options['--date'] = $this->option('date');
        }

        $this->call('phinx:migrate:rollback', $options);
        $this->call('phinx:migrate');
    }
}
