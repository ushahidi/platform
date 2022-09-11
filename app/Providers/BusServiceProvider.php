<?php

namespace Ushahidi\App\Providers;

use Illuminate\Support\ServiceProvider;
use Ushahidi\App\Bus\Command\CommandBus;
use Ushahidi\App\Bus\Command\Example\ExampleCommand;
use Ushahidi\App\Bus\Command\Example\ExampleCommandHandler;
use Ushahidi\App\Bus\Query\Example\ExampleQuery;
use Ushahidi\App\Bus\Query\Example\ExampleQueryHandler;
use Ushahidi\App\Bus\Query\QueryBus;

class BusServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->registerCommands();
        $this->registerQueries();
    }

    private function registerCommands(): void
    {
        $this->app->singleton(CommandBus::class, function ($app) {
            $commandBus = new CommandBus($app);

            $commandBus->register(ExampleCommand::class, ExampleCommandHandler::class);

            return $commandBus;
        });
    }

    private function registerQueries(): void
    {
        $this->app->singleton(QueryBus::class, function ($app) {
            $queryBus = new QueryBus($app);

            $queryBus->register(ExampleQuery::class, ExampleQueryHandler::class);

            return $queryBus;
        });
    }
}
