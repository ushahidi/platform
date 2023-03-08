<?php

namespace App\Providers;

use App\Bus\Command\CommandBus;
use App\Bus\Command\Example\ExampleCommand;
use App\Bus\Command\Example\ExampleCommandHandler;
use App\Bus\Query\Example\ExampleQuery;
use App\Bus\Query\Example\ExampleQueryHandler;
use Ushahidi\Modules\V5\Actions\Datasource\Handlers\FetchDataSourceQueryHandler;
use Ushahidi\Modules\V5\Actions\Datasource\Handlers\SearchDataSourcesQueryHandler;
use Ushahidi\Modules\V5\Actions\Datasource\Queries\FetchDataSourceQuery;
use Ushahidi\Modules\V5\Actions\Datasource\Queries\SearchDataSourcesQuery;
use Ushahidi\Modules\V5\Actions\Post\Queries\FindPostByIdQuery;
use Ushahidi\Modules\V5\Actions\Post\Handlers\FindPostByIdQueryHandler;
use Ushahidi\Modules\V5\Actions\Post\Queries\ListPostsQuery;
use Ushahidi\Modules\V5\Actions\Post\Handlers\ListPostsQueryHandler;
use Ushahidi\Modules\V5\Actions\Tos\Commands\CreateTosCommand;
use Ushahidi\Modules\V5\Actions\Tos\Handlers\CreateTosCommandHandler;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosQuery;
use Ushahidi\Modules\V5\Actions\Tos\Handlers\FetchTosQueryHandler;
use Ushahidi\Modules\V5\Actions\Tos\Queries\FetchTosByIdQuery;
use Ushahidi\Modules\V5\Actions\Tos\Handlers\FetchTosByIdQueryHandler;

use App\Bus\Query\QueryBus;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Modules\V5\Actions\CountryCode\Queries\FetchCountryCodeByIdQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\Queries\FetchCountryCodeQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\Handlers\FetchCountryCodeByIdQueryHandler;
use Ushahidi\Modules\V5\Actions\CountryCode\Handlers\FetchCountryCodeQueryHandler;
use Ushahidi\Modules\V5\Actions\User;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsQuery;
use Ushahidi\Modules\V5\Actions\Permissions\Handlers\FetchPermissionsQueryHandler;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsByIdQuery;
use Ushahidi\Modules\V5\Actions\Permissions\Handlers\FetchPermissionsByIdQueryHandler;
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

            $commandBus->register(CreateTosCommand::class, CreateTosCommandHandler::class);

            return $commandBus;
        });
    }

    private function registerQueries(): void
    {
        $this->app->singleton(QueryBus::class, function ($app) {
            $queryBus = new QueryBus($app);

            $queryBus->register(FetchCountryCodeQuery::class, FetchCountryCodeQueryHandler::class);
            $queryBus->register(FetchCountryCodeByIdQuery::class, FetchCountryCodeByIdQueryHandler::class);
            $queryBus->register(ExampleQuery::class, ExampleQueryHandler::class);
            $queryBus->register(FetchPermissionsQuery::class, FetchPermissionsQueryHandler::class);
            $queryBus->register(FetchPermissionsByIdQuery::class, FetchPermissionsByIdQueryHandler::class);

            $queryBus->register(
                Role\Queries\FetchRoleQuery::class,
                Role\Handlers\FetchRoleQueryHandler::class
            );
            $queryBus->register(
                Role\Queries\FetchRoleByIdQuery::class,
                Role\Handlers\FetchRoleByIdQueryHandler::class
            );

            $queryBus->register(FetchTosQuery::class, FetchTosQueryHandler::class);
            $queryBus->register(FetchTosByIdQuery::class, FetchTosByIdQueryHandler::class);

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

            $queryBus->register(
                FetchDataSourceQuery::class,
                FetchDataSourceQueryHandler::class
            );

            $queryBus->register(
                SearchDataSourcesQuery::class,
                SearchDataSourcesQueryHandler::class
            );

            $queryBus->register(
                FindPostByIdQuery::class,
                FindPostByIdQueryHandler::class
            );

            $queryBus->register(
                ListPostsQuery::class,
                ListPostsQueryHandler::class
            );

            return $queryBus;
        });
    }
}
