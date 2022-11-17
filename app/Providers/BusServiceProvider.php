<?php

namespace App\Providers;

use App\Bus\Command\CommandBus;
use App\Bus\Command\Example\ExampleCommand;
use App\Bus\Command\Example\ExampleCommandHandler;
use App\Bus\Query\Example\ExampleQuery;
use App\Bus\Query\Example\ExampleQueryHandler;
use App\Bus\Query\QueryBus;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Modules\V5\Actions\User;

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
                User\Commands\CreateUserCommand::class,
                User\Handlers\CreateUserCommandHandler::class
            );
            $commandBus->register(
                User\Commands\UpdateUserCommand::class,
                User\Handlers\UpdateUserCommandHandler::class
            );
            $commandBus->register(
                User\Commands\DeleteUserCommand::class,
                User\Handlers\DeleteUserCommandHandler::class
            );

            $commandBus->register(
                User\Commands\CreateUserSettingCommand::class,
                User\Handlers\CreateUserSettingCommandHandler::class
            );
            $commandBus->register(
                User\Commands\UpdateUserSettingCommand::class,
                User\Handlers\UpdateUserSettingCommandHandler::class
            );
            $commandBus->register(
                User\Commands\DeleteUserSettingCommand::class,
                User\Handlers\DeleteUserSettingCommandHandler::class
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
                User\Queries\FetchUserQuery::class,
                User\Handlers\FetchUserQueryHandler::class
            );
            $queryBus->register(
                User\Queries\FetchUserByIdQuery::class,
                User\Handlers\FetchUserByIdQueryHandler::class
            );
            
            $queryBus->register(
                User\Queries\FetchUserSettingQuery::class,
                User\Handlers\FetchUserSettingQueryHandler::class
            );
            $queryBus->register(
                User\Queries\FetchUserSettingByIdQuery::class,
                User\Handlers\FetchUserSettingByIdQueryHandler::class
            );

            return $queryBus;
        });
    }
}
