<?php

namespace App\Providers;

use App\Bus\Command\CommandBus;
use App\Bus\Command\Example\ExampleCommand;
use App\Bus\Command\Example\ExampleCommandHandler;
use App\Bus\Query\Example\ExampleQuery;
use App\Bus\Query\Example\ExampleQueryHandler;
use App\Bus\Query\QueryBus;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Modules\V5\Actions\Role;

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
            
            $commandBus->register(
                Role\Commands\CreateRoleCommand::class,
                Role\Handlers\CreateRoleCommandHandler::class
            );
            $commandBus->register(
                Role\Commands\UpdateRoleCommand::class,
                Role\Handlers\UpdateRoleCommandHandler::class
            );
            $commandBus->register(
                Role\Commands\DeleteRoleCommand::class,
                Role\Handlers\DeleteRoleCommandHandler::class
            );

            $commandBus->register(
                Role\Commands\CreateRolePermissionCommand::class,
                Role\Handlers\CreateRolePermissionCommandHandler::class
            );
            $commandBus->register(
                Role\Commands\DeleteRolePermissionByRoleCommand::class,
                Role\Handlers\DeleteRolePermissionByRoleCommandHandler::class
            );

            return $commandBus;
        });
    }

    private function registerQueries(): void
    {
        $this->app->singleton(QueryBus::class, function ($app) {
            $queryBus = new QueryBus($app);

            $queryBus->register(ExampleQuery::class, ExampleQueryHandler::class);
               
            $queryBus->register(
                Role\Queries\FetchRoleQuery::class,
                Role\Handlers\FetchRoleQueryHandler::class
            );
            $queryBus->register(
                Role\Queries\FetchRoleByIdQuery::class,
                Role\Handlers\FetchRoleByIdQueryHandler::class
            );


            return $queryBus;
        });
    }
}
