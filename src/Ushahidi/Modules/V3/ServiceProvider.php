<?php

namespace Ushahidi\Modules\V3;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ushahidi\Modules\V3\Console;
use Ushahidi\Modules\V3\Factory\UsecaseFactory;
use Ushahidi\Modules\V3\Repository\TosRepository;
use Ushahidi\Modules\V5\Http\Middleware\V5GlobalScopes;
use Ushahidi\Core\Tool\Verifier;
use Ushahidi\Core\Usecase\User\LoginUser;
use Ushahidi\Core\Usecase\Post\ExportPost;
use Ushahidi\Core\Usecase\Export\Job\PostCount;
use Ushahidi\Core\Usecase\Message\ReceiveMessage;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\MediaRepository;
use Ushahidi\Core\Entity\ApiKeyRepository;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Entity\ExportBatchRepository;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app[Kernel::class]->pushMiddleware(V5GlobalScopes::class);

        Route::prefix('api')
            ->middleware('api')
            ->namespace('Ushahidi\Modules\V3\Http\Controllers')
            ->group(__DIR__ . '/routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServicesFromAura();

        $this->registerCommands();
    }

    public function registerServicesFromAura()
    {
        $this->app->singleton(UsecaseFactory::class, function ($app) {
            // Just return it from AuraDI
            return service('factory.usecase');
        });

        $this->app->singleton(UserRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.user');
        });

        $this->app->singleton(ApiKeyRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.apikey');
        });

        $this->app->singleton(MessageRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.message');
        });

        $this->app->singleton(ConfigRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.config');
        });

        $this->app->singleton(ContactRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.contact');
        });

        $this->app->singleton(PostRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.post');
        });

        $this->app->singleton(ExportJobRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.export_job');
        });

        $this->app->singleton(ExportBatchRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.export_batch');
        });

        $this->app->singleton(TargetedSurveyStateRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.targeted_survey_state');
        });

        $this->app->singleton(FormAttributeRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.form_attribute');
        });

        $this->app->singleton(MediaRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.media');
        });

        $this->app->singleton(SetRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.set');
        });

        $this->app->singleton(TosRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.tos');
        });

        $this->app->singleton(Verifier::class, function ($app) {
            // Just return it from AuraDI
            return service('tool.verifier');
        });

        $this->app->singleton(LoginUser::class, function ($app) {
            return service('factory.usecase')
                // Override action
                ->get('users', 'login');
        });

        $this->app->singleton(ReceiveMessage::class, function ($app) {
            return service('factory.usecase')
                // Override action
                ->get('messages', 'receive');
        });

        $this->app->singleton(PostCount::class, function ($app) {
            return service('factory.usecase')
                // Override action
                ->get('export_jobs', 'post-count')
                // Override authorizer
                ->setAuthorizer(service('authorizer.external_auth')); // @todo remove the need for this?
        });

        $this->app->singleton(ExportPost::class, function ($app) {
            return service('factory.usecase')
                ->get('posts_export', 'export')
                ->setAuthorizer(service('authorizer.export_job'));
        });
    }

    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\NotificationCommand::class,
                Console\WebhookCommand::class,
                Console\ImportMediaCommand::class,
                Console\ObfuscateDataCommand::class,
                Console\PostExporterCommand::class,
                Console\MigrateCommand::class,
                Console\MigrateInstallCommand::class,
                Console\MigrateMakeCommand::class,
                Console\MigrateRefreshCommand::class,
                Console\MigrateResetCommand::class,
                Console\MigrateRollbackCommand::class,
                Console\MigrateStatusCommand::class,
                Console\SeedCommand::class,
                Console\SeedMakeCommand::class,
            ]);
        }
    }
}
