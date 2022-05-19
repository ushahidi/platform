<?php

namespace Ushahidi\App\Providers;

use Illuminate\Support\ServiceProvider;
use Ushahidi\App\DataSource\DataSourceServiceProvider;
use Ushahidi\App\Multisite\MultisiteServiceProvider;
use Ushahidi\App\Providers\FilesystemServiceProvider;
use Ushahidi\App\Tools\Features;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Contracts\Repository\Entity\ContactRepository;
use Ushahidi\Contracts\Repository\Entity\ExportBatchRepository;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;
use Ushahidi\Contracts\Repository\Entity\FormAttributeRepository;
use Ushahidi\Contracts\Repository\Entity\MessageRepository;
use Ushahidi\Contracts\Repository\Entity\PostRepository;
use Ushahidi\Contracts\Repository\Entity\TargetedSurveyStateRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;
use Ushahidi\Core\Tools\Verifier;
use Ushahidi\Core\Usecase\Export\Job\PostCount;
use Ushahidi\Core\Usecase\Post\Export;
use Ushahidi\Factory\UsecaseFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServicesFromAura();

        // $this->registerFilesystem();
        // $this->registerMailer();

        $this->registerMultisite();
        $this->registerDataSources();

        $this->registerFeatures();
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

    public function registerMailer()
    {
        // Add mailer
        $this->app->singleton('mailer', function ($app) {
            return $app->make(
                'mail',
                \Illuminate\Mail\MailServiceProvider::class,
                'mailer'
            );
        });
    }

    public function registerFilesystem()
    {
        // Add filesystem
        $this->app->singleton('filesystem', function ($app) {
            return $app->make(
                'filesystems',
                FilesystemServiceProvider::class,
                'filesystem'
            );
        });
    }

    public function registerMultisite()
    {
        $this->app->register(MultisiteServiceProvider::class);
    }

    public function registerDataSources()
    {
        $this->app->register(DataSourceServiceProvider::class);
    }

    public function registerFeatures()
    {
        $this->app->singleton('features', function ($app) {
            return new Features($app[ConfigRepository::class]);
        });
    }
}
