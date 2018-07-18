<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;

class MigrateInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the migration repository.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('This command isn\'t needed for phinx. No work done');
    }
}
