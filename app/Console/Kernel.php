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
        \Ushahidi\Console\Command\ConfigSet::class,
        \Ushahidi\Console\Command\ConfigGet::class,
        \Ushahidi\Console\Command\Import::class,
        \Ushahidi\Console\Command\UserCreate::class,
        \Ushahidi\Console\Command\UserDelete::class,
        \Ushahidi\Console\Command\Notification::class,
        \Ushahidi\Console\Command\PostExporter::class,
        \Ushahidi\Console\Command\SavedSearch::class,
        \Ushahidi\Console\Command\Webhook::class
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
