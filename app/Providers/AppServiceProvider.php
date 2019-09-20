<?php

namespace Ushahidi\App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('cdn');
        $this->app->configure('filesystems');
        $this->app->configure('media');
        $this->app->configure('ratelimiter');
        $this->app->configure('multisite');
        $this->app->configure('ohanzee-db');
        $this->app->configure('services');

        $this->registerServicesFromAura();

        $this->registerFilesystem();
        $this->registerMailer();

        $this->registerMultisite();
        $this->registerDataSources();

        $this->registerFeatures();
    }

    public function registerServicesFromAura()
    {
        $this->app->singleton(\Ushahidi\Factory\UsecaseFactory::class, function ($app) {
            // Just return it from AuraDI
            return service('factory.usecase');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\MessageRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.message');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\ConfigRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.config');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\ContactRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.contact');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\PostRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.post');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\ExportJobRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.export_job');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\ExportBatchRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.export_batch');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\TargetedSurveyStateRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.targeted_survey_state');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\FormAttributeRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.form_attribute');
        });

        $this->app->singleton(\Ushahidi\Core\Tool\Verifier::class, function ($app) {
            // Just return it from AuraDI
            return service('tool.verifier');
        });

        $this->app->singleton(\Ushahidi\Core\Usecase\Export\Job\PostCount::class, function ($app) {
            return service('factory.usecase')
                    // Override action
                    ->get('export_jobs', 'post-count')
                    // Override authorizer
                    ->setAuthorizer(service('authorizer.external_auth')) // @todo remove the need for this?
                    ;
        });

        $this->app->singleton(\Ushahidi\Core\Usecase\Post\Export::class, function ($app) {
            return service('factory.usecase')
                    ->get('posts_export', 'export')
                    ->setAuthorizer(service('authorizer.export_job'))
                    ;
        });
    }

    public function registerMailer()
    {
        // Add mailer
        $this->app->singleton('mailer', function ($app) {
            return $app->loadComponent(
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
            return $app->loadComponent(
                'filesystems',
                \Illuminate\Filesystem\FilesystemServiceProvider::class,
                'filesystem'
            );
        });
    }

    public function registerMultisite()
    {
        $this->app->register(\Ushahidi\App\Multisite\MultisiteServiceProvider::class);
    }

    public function registerDataSources()
    {
        $this->app->register(\Ushahidi\App\DataSource\DataSourceServiceProvider::class);
    }

    public function registerFeatures()
    {
        $this->app->singleton('features', function ($app) {
            return new \Ushahidi\App\Tools\Features($app[\Ushahidi\Core\Entity\ConfigRepository::class]);
        });
    }
}
