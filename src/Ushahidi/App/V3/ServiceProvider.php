<?php

namespace Ushahidi\App\V3;

use Ushahidi\Core\Tool\Verifier;
use Ushahidi\Factory\UsecaseFactory;
use Illuminate\Support\Facades\Route;
use Ushahidi\Core\Usecase\Post\Export;
use Ushahidi\Core\Usecase\Export\Job\PostCount;
use Ushahidi\Contracts\Repository\Entity\PostRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;
use Ushahidi\Contracts\Repository\Entity\MediaRepository;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Contracts\Repository\Entity\ContactRepository;
use Ushahidi\Contracts\Repository\Entity\MessageRepository;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ushahidi\Contracts\Repository\Entity\ExportBatchRepository;
use Ushahidi\Contracts\Repository\Entity\FormAttributeRepository;
use Ushahidi\Contracts\Repository\Entity\TargetedSurveyStateRepository;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace('Ushahidi\App\V3\Http\Controllers')
            ->group(__DIR__.'/routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServicesFromAura();
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

        $this->app->singleton(Verifier::class, function ($app) {
            // Just return it from AuraDI
            return service('tool.verifier');
        });

        $this->app->singleton(PostCount::class, function ($app) {
            return service('factory.usecase')
                    // Override action
                    ->get('export_jobs', 'post-count')
                    // Override authorizer
                    ->setAuthorizer(service('authorizer.external_auth')); // @todo remove the need for this?
        });

        $this->app->singleton(Export::class, function ($app) {
            return service('factory.usecase')
                    ->get('posts_export', 'export')
                    ->setAuthorizer(service('authorizer.export_job'));
        });
    }
}
