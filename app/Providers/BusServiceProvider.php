<?php

namespace App\Providers;

use App\Bus\Command\CommandBus;
use App\Bus\Command\Example\ExampleCommand;
use App\Bus\Command\Example\ExampleCommandHandler;
use App\Bus\Query\Example\ExampleQuery;
use App\Bus\Query\Example\ExampleQueryHandler;
use Ushahidi\Modules\V5\Actions\Tos\Commands\CreateTosCommand;
use Ushahidi\Modules\V5\Actions\Tos\Handlers\CreateTosCommandHandler;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosQuery;
use Ushahidi\Modules\V5\Actions\Tos\Handlers\FetchTosQueryHandler;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosByIdQuery;
use Ushahidi\Modules\V5\Actions\Tos\Handlers\FetchTosByIdQueryHandler;

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
            $queryBus->register(FetchTosQuery::class, FetchTosQueryHandler::class);
            $queryBus->register(FetchTosByIdQuery::class, FetchTosByIdQueryHandler::class);

            return $queryBus;
        });
    }
}
