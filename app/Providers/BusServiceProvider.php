<?php

namespace App\Providers;

use App\Bus\Command\CommandBus;
use App\Bus\Command\Example\ExampleCommand;
use App\Bus\Command\Example\ExampleCommandHandler;
use App\Bus\Query\Example\ExampleQuery;
use App\Bus\Query\Example\ExampleQueryHandler;
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
use Ushahidi\Modules\V5\Actions\Translation\Commands\AddTranslationCommand;
use Ushahidi\Modules\V5\Actions\Translation\Handlers\AddTranslationCommandHandler;
use Ushahidi\Modules\V5\Actions\User;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsQuery;
use Ushahidi\Modules\V5\Actions\Permissions\Handlers\FetchPermissionsQueryHandler;
use Ushahidi\Modules\V5\Actions\Permissions\Queries\FetchPermissionsByIdQuery;
use Ushahidi\Modules\V5\Actions\Permissions\Handlers\FetchPermissionsByIdQueryHandler;
use Ushahidi\Modules\V5\Actions\Role;
use Ushahidi\Modules\V5\Actions\Category;
use Ushahidi\Modules\V5\Actions\Survey;
use Ushahidi\Modules\V5\Actions\SavedSearch;
use Ushahidi\Modules\V5\Actions\Collection;
use Ushahidi\Modules\V5\Actions\Post;
use Ushahidi\Modules\V5\Actions\Config;
use Ushahidi\Modules\V5\Actions\Contact;
use Ushahidi\Modules\V5\Actions\Message;
use Ushahidi\Modules\V5\Actions\Notification;
use Ushahidi\Modules\V5\Actions\Layer;
use Ushahidi\Modules\V5\Actions\CSV;
use Ushahidi\Modules\V5\Actions\Export;
use Ushahidi\Modules\V5\Actions\Auth;
use Ushahidi\Modules\V5\Actions\Media;
use Ushahidi\Modules\V5\Actions\Apikey;

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


            $commandBus->register(
                Survey\Commands\CreateTaskCommand::class,
                Survey\Handlers\CreateTaskCommandHandler::class
            );
            $commandBus->register(
                Survey\Commands\DeleteTasksCommand::class,
                Survey\Handlers\DeleteTasksCommandHandler::class
            );
            $commandBus->register(
                Survey\Commands\UpdateTaskCommand::class,
                Survey\Handlers\UpdateTaskCommandHandler::class
            );


            $commandBus->register(
                Survey\Commands\CreateSurveyCommand::class,
                Survey\Handlers\CreateSurveyCommandHandler::class
            );
            $commandBus->register(
                Survey\Commands\UpdateSurveyCommand::class,
                Survey\Handlers\UpdateSurveyCommandHandler::class
            );
            $commandBus->register(
                Survey\Commands\DeleteSurveyCommand::class,
                Survey\Handlers\DeleteSurveyCommandHandler::class
            );

            $commandBus->register(
                Survey\Commands\CreateSurveyRoleCommand::class,
                Survey\Handlers\CreateSurveyRoleCommandHandler::class
            );

            $commandBus->register(
                Survey\Commands\DeleteSurveyRolesBySurveyIDCommand::class,
                Survey\Handlers\DeleteSurveyRolesBySurveyIDCommandHandler::class
            );



            $commandBus->register(
                SavedSearch\Commands\CreateSavedSearchCommand::class,
                SavedSearch\Handlers\CreateSavedSearchCommandHandler::class
            );
            $commandBus->register(
                SavedSearch\Commands\UpdateSavedSearchCommand::class,
                SavedSearch\Handlers\UpdateSavedSearchCommandHandler::class
            );
            $commandBus->register(
                SavedSearch\Commands\DeleteSavedSearchCommand::class,
                SavedSearch\Handlers\DeleteSavedSearchCommandHandler::class
            );


            $commandBus->register(
                Collection\Commands\CreateCollectionCommand::class,
                Collection\Handlers\CreateCollectionCommandHandler::class
            );
            $commandBus->register(
                Collection\Commands\UpdateCollectionCommand::class,
                Collection\Handlers\UpdateCollectionCommandHandler::class
            );
            $commandBus->register(
                Collection\Commands\DeleteCollectionCommand::class,
                Collection\Handlers\DeleteCollectionCommandHandler::class
            );


            $commandBus->register(CreateTosCommand::class, CreateTosCommandHandler::class);
            
            $commandBus->register(
                AddTranslationCommand::class,
                AddTranslationCommandHandler::class
            );

            $commandBus->register(
                Category\Commands\StoreCategoryCommand::class,
                Category\Handlers\StoreCategoryCommandHandler::class
            );

           

            $commandBus->register(
                Category\Commands\DeleteCategoryCommand::class,
                Category\Handlers\DeleteCategoryCommandHandler::class
            );
            $commandBus->register(
                Category\Commands\UpdateCategoryCommand::class,
                Category\Handlers\UpdateCategoryCommandHandler::class
            );

            $commandBus->register(
                Post\Commands\CreatePostCommand::class,
                Post\Handlers\CreatePostCommandHandler::class
            );
            $commandBus->register(
                Post\Commands\UpdatePostCommand::class,
                Post\Handlers\UpdatePostCommandHandler::class
            );
            $commandBus->register(
                Post\Commands\DeletePostCommand::class,
                Post\Handlers\DeletePostCommandHandler::class
            );

            $commandBus->register(
                Collection\Commands\CreateCollectionPostCommand::class,
                Collection\Handlers\CreateCollectionPostCommandHandler::class
            );
            $commandBus->register(
                Collection\Commands\DeleteCollectionPostCommand::class,
                Collection\Handlers\DeleteCollectionPostCommandHandler::class
            );
            $commandBus->register(
                Post\Commands\UpdatePostLockCommand::class,
                Post\Handlers\UpdatePostLockCommandHandler::class
            );
            $commandBus->register(
                Post\Commands\DeletePostLockCommand::class,
                Post\Handlers\DeletePostLockCommandHandler::class
            );
            $commandBus->register(
                Config\Commands\UpdateConfigCommand::class,
                Config\Handlers\UpdateConfigCommandHandler::class
            );

            $commandBus->register(
                Contact\Commands\CreateContactCommand::class,
                Contact\Handlers\CreateContactCommandHandler::class
            );
            $commandBus->register(
                Contact\Commands\UpdateContactCommand::class,
                Contact\Handlers\UpdateContactCommandHandler::class
            );
            $commandBus->register(
                Contact\Commands\DeleteContactCommand::class,
                Contact\Handlers\DeleteContactCommandHandler::class
            );


            $commandBus->register(
                Message\Commands\CreateMessageCommand::class,
                Message\Handlers\CreateMessageCommandHandler::class
            );
            $commandBus->register(
                Message\Commands\UpdateMessageCommand::class,
                Message\Handlers\UpdateMessageCommandHandler::class
            );
            $commandBus->register(
                Message\Commands\DeleteMessageCommand::class,
                Message\Handlers\DeleteMessageCommandHandler::class
            );



            $commandBus->register(
                Notification\Commands\CreateNotificationCommand::class,
                Notification\Handlers\CreateNotificationCommandHandler::class
            );
            $commandBus->register(
                Notification\Commands\UpdateNotificationCommand::class,
                Notification\Handlers\UpdateNotificationCommandHandler::class
            );
            $commandBus->register(
                Notification\Commands\DeleteNotificationCommand::class,
                Notification\Handlers\DeleteNotificationCommandHandler::class
            );



            $commandBus->register(
                Layer\Commands\CreateLayerCommand::class,
                Layer\Handlers\CreateLayerCommandHandler::class
            );
            $commandBus->register(
                Layer\Commands\UpdateLayerCommand::class,
                Layer\Handlers\UpdateLayerCommandHandler::class
            );
            $commandBus->register(
                Layer\Commands\DeleteLayerCommand::class,
                Layer\Handlers\DeleteLayerCommandHandler::class
            );


            $commandBus->register(
                CSV\Commands\CreateCSVCommand::class,
                CSV\Handlers\CreateCSVCommandHandler::class
            );
            $commandBus->register(
                CSV\Commands\UpdateCSVCommand::class,
                CSV\Handlers\UpdateCSVCommandHandler::class
            );
            $commandBus->register(
                CSV\Commands\DeleteCSVCommand::class,
                CSV\Handlers\DeleteCSVCommandHandler::class
            );


            $commandBus->register(
                Export\Commands\CreateExportJobCommand::class,
                Export\Handlers\CreateExportJobCommandHandler::class
            );
            $commandBus->register(
                Export\Commands\UpdateExportJobCommand::class,
                Export\Handlers\UpdateExportJobCommandHandler::class
            );
            $commandBus->register(
                Export\Commands\DeleteExportJobCommand::class,
                Export\Handlers\DeleteExportJobCommandHandler::class
            );

            $commandBus->register(
                Auth\Commands\PasswordResetCommand::class,
                Auth\Handlers\PasswordResetCommandHandler::class
            );
            $commandBus->register(
                Auth\Commands\PasswordResetConfirmCommand::class,
                Auth\Handlers\PasswordResetConfirmCommandHandler::class
            );
            $commandBus->register(
                Auth\Commands\RegisterCommand::class,
                Auth\Handlers\RegisterCommandHandler::class
            );


            $commandBus->register(
                Media\Commands\CreateMediaCommand::class,
                Media\Handlers\CreateMediaCommandHandler::class
            );
            $commandBus->register(
                Media\Commands\UpdateMediaCommand::class,
                Media\Handlers\UpdateMediaCommandHandler::class
            );
            $commandBus->register(
                Media\Commands\DeleteMediaCommand::class,
                Media\Handlers\DeleteMediaCommandHandler::class
            );


            $commandBus->register(
                Apikey\Commands\CreateApikeyCommand::class,
                Apikey\Handlers\CreateApikeyCommandHandler::class
            );
            $commandBus->register(
                Apikey\Commands\UpdateApikeyCommand::class,
                Apikey\Handlers\UpdateApikeyCommandHandler::class
            );
            $commandBus->register(
                Apikey\Commands\DeleteApikeyCommand::class,
                Apikey\Handlers\DeleteApikeyCommandHandler::class
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
                FetchCountryCodeQuery::class,
                FetchCountryCodeQueryHandler::class
            );
            $queryBus->register(
                FetchCountryCodeByIdQuery::class,
                FetchCountryCodeByIdQueryHandler::class
            );
            $queryBus->register(
                FetchPermissionsQuery::class,
                FetchPermissionsQueryHandler::class
            );
            $queryBus->register(
                FetchPermissionsByIdQuery::class,
                FetchPermissionsByIdQueryHandler::class
            );

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
                Category\Queries\FetchCategoryByIdQuery::class,
                Category\Handlers\FetchCategoryByIdQueryHandler::class
            );

            $queryBus->register(
                Category\Queries\FetchAllCategoriesQuery::class,
                Category\Handlers\FetchAllCategoriesQueryHandler::class
            );

            $queryBus->register(
                Survey\Queries\FetchRolesCanCreateSurveyPostsQuery::class,
                Survey\Handlers\FetchRolesCanCreateSurveyPostsQueryHandler::class
            );

            $queryBus->register(
                Survey\Queries\FetchTasksBySurveyIdQuery::class,
                Survey\Handlers\FetchTasksBySurveyIdQueryHandler::class
            );

            $queryBus->register(
                Survey\Queries\FetchSurveyQuery::class,
                Survey\Handlers\FetchSurveyQueryHandler::class
            );
            $queryBus->register(
                Survey\Queries\FetchSurveyByIdQuery::class,
                Survey\Handlers\FetchSurveyByIdQueryHandler::class
            );
            $queryBus->register(
                Survey\Queries\FetchSurveyStatsQuery::class,
                Survey\Handlers\FetchSurveyStatsQueryHandler::class
            );



            $queryBus->register(
                SavedSearch\Queries\FetchSavedSearchQuery::class,
                SavedSearch\Handlers\FetchSavedSearchQueryHandler::class
            );
            $queryBus->register(
                SavedSearch\Queries\FetchSavedSearchByIdQuery::class,
                SavedSearch\Handlers\FetchSavedSearchByIdQueryHandler::class
            );


            $queryBus->register(
                Collection\Queries\FetchCollectionQuery::class,
                Collection\Handlers\FetchCollectionQueryHandler::class
            );
            $queryBus->register(
                Collection\Queries\FetchCollectionByIdQuery::class,
                Collection\Handlers\FetchCollectionByIdQueryHandler::class
            );

            $queryBus->register(
                FindPostByIdQuery::class,
                FindPostByIdQueryHandler::class
            );

            $queryBus->register(
                ListPostsQuery::class,
                ListPostsQueryHandler::class
            );

            $queryBus->register(
                Collection\Queries\FetchCollectionPostByIdQuery::class,
                Collection\Handlers\FetchCollectionPostByIdQueryHandler::class
            );

            $queryBus->register(
                Post\Queries\FetchPostLockByPostIdQuery::class,
                Post\Handlers\FetchPostLockByPostIdQueryHandler::class
            );
            $queryBus->register(
                Post\Queries\FindPostGeometryByIdQuery::class,
                Post\Handlers\FindPostGeometryByIdQueryHandler::class
            );
            $queryBus->register(
                Post\Queries\ListPostsGeometryQuery::class,
                Post\Handlers\ListPostsGeometryQueryHandler::class
            );
            $queryBus->register(
                Post\Queries\PostsStatsQuery::class,
                Post\Handlers\PostsStatsQueryHandler::class
            );

            $queryBus->register(
                Config\Queries\ListConfigsQuery::class,
                Config\Handlers\ListConfigsQueryHandler::class
            );
            $queryBus->register(
                Config\Queries\FindConfigByNameQuery::class,
                Config\Handlers\FindConfigByNameQueryHandler::class
            );

            $queryBus->register(
                Contact\Queries\FetchContactQuery::class,
                Contact\Handlers\FetchContactQueryHandler::class
            );
            $queryBus->register(
                Contact\Queries\FetchContactByIdQuery::class,
                Contact\Handlers\FetchContactByIdQueryHandler::class
            );


            $queryBus->register(
                Message\Queries\FetchMessageQuery::class,
                Message\Handlers\FetchMessageQueryHandler::class
            );
            $queryBus->register(
                Message\Queries\FetchMessageByIdQuery::class,
                Message\Handlers\FetchMessageByIdQueryHandler::class
            );


            $queryBus->register(
                Notification\Queries\FetchNotificationQuery::class,
                Notification\Handlers\FetchNotificationQueryHandler::class
            );
            $queryBus->register(
                Notification\Queries\FetchNotificationByIdQuery::class,
                Notification\Handlers\FetchNotificationByIdQueryHandler::class
            );


            $queryBus->register(
                Layer\Queries\FetchLayerQuery::class,
                Layer\Handlers\FetchLayerQueryHandler::class
            );
            $queryBus->register(
                Layer\Queries\FetchLayerByIdQuery::class,
                Layer\Handlers\FetchLayerByIdQueryHandler::class
            );


            $queryBus->register(
                CSV\Queries\FetchCSVQuery::class,
                CSV\Handlers\FetchCSVQueryHandler::class
            );
            $queryBus->register(
                CSV\Queries\FetchCSVByIdQuery::class,
                CSV\Handlers\FetchCSVByIdQueryHandler::class
            );


            $queryBus->register(
                Export\Queries\FetchExportJobQuery::class,
                Export\Handlers\FetchExportJobQueryHandler::class
            );
            $queryBus->register(
                Export\Queries\FetchExportJobByIdQuery::class,
                Export\Handlers\FetchExportJobByIdQueryHandler::class
            );


            $queryBus->register(
                Media\Queries\FetchMediaByIdQuery::class,
                Media\Handlers\FetchMediaByIdQueryHandler::class
            );

            $queryBus->register(
                Apikey\Queries\FetchApikeyQuery::class,
                Apikey\Handlers\FetchApikeyQueryHandler::class
            );
            $queryBus->register(
                Apikey\Queries\FetchApikeyByIdQuery::class,
                Apikey\Handlers\FetchApikeyByIdQueryHandler::class
            );

            return $queryBus;
        });
    }
}
