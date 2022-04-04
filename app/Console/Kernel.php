<?php

namespace Ushahidi\App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RoutesCommand::class,
        Commands\MigrateCommand::class,
        Commands\MigrateInstallCommand::class,
        Commands\MigrateMakeCommand::class,
        Commands\MigrateRefreshCommand::class,
        Commands\MigrateResetCommand::class,
        Commands\MigrateRollbackCommand::class,
        Commands\MigrateStatusCommand::class,
        Commands\SeedMakeCommand::class,
        Commands\SeedCommand::class,
        Commands\EnvironmentVerifyCommand::class,
        Commands\ApikeySetCommand::class,
        Commands\ConfigSetCommand::class,
        Commands\ConfigGetCommand::class,
        Commands\UserCreateCommand::class,
        Commands\UserDeleteCommand::class,
        Commands\NotificationCommand::class,
        Commands\PostExporterCommand::class,
        Commands\SavedSearchCommand::class,
        Commands\WebhookCommand::class,
        Commands\ObfuscateDataCommand::class,
        Commands\TestMultisiteJobCommand::class,
    ];

    /**
     * Indicates if facade aliases are enabled for the console.
     *
     * @var bool
     */
    protected $aliases = false;

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
