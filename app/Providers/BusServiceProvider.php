<?php

namespace App\Providers;

use App\Bus\Command\CommandBus;
use App\Bus\Command\Example\ExampleCommand;
use App\Bus\Command\Example\ExampleCommandHandler;
use App\Bus\Query\Example\ExampleQuery;
use App\Bus\Query\Example\ExampleQueryHandler;
use Ushahidi\Modules\V5\Commands\Tos\CreateTosCommand;
use Ushahidi\Modules\V5\Commands\Tos\CreateTosCommandHandler;
use Ushahidi\Modules\V5\Queries\Tos\GetTosQuery;
use Ushahidi\Modules\V5\Queries\Tos\GetTosQueryHandler;

use App\Bus\Query\QueryBus;
use Illuminate\Support\ServiceProvider;

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
            $commandBus->register(CreateTosCommand::class, CreateTosCommandHandler::class);
            
            return $commandBus;
        });
    }

    private function registerQueries(): void
    {
        $this->app->singleton(QueryBus::class, function ($app) {
            $queryBus = new QueryBus($app);

            $queryBus->register(ExampleQuery::class, ExampleQueryHandler::class);
            $queryBus->register(GetTosQuery::class, GetTosQueryHandler::class);

            return $queryBus;
        });
    }
}
