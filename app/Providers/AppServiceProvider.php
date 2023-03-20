<?php

namespace App\Providers;

use Ushahidi\Core\Tool\Features;
use Ushahidi\Core\Tool\Verifier;
use Ushahidi\Factory\UsecaseFactory;
use Ushahidi\Addons\Mteja\MtejaSource;
use Ushahidi\Core\Usecase\Post\Export;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Ushahidi\Core\Usecase\Export\Job\PostCount;
use Ushahidi\Addons\AfricasTalking\AfricasTalkingSource;
use Ushahidi\Contracts\Repository\Entity\PostRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Modules\V5\Repository\CountryCode\CountryCodeRepository;
use Ushahidi\Modules\V5\Repository\CountryCode\EloquentCountryCodeRepository;
use Ushahidi\Modules\V5\Repository\User;
use Ushahidi\Modules\V5\Repository\Permissions\PermissionsRepository;
use Ushahidi\Modules\V5\Repository\Permissions\EloquentPermissionsRepository;
use Ushahidi\Modules\V5\Repository\Role\RoleRepository;
use Ushahidi\Modules\V5\Repository\Role\EloquentRoleRepository;
use Ushahidi\Modules\V5\Repository\Tos\TosRepository;
use Ushahidi\Modules\V5\Repository\Tos\EloquentTosRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * For now this configuration is temporary,
         * should be moved to an isolated place within the addon directory
         */
        $this->app['datasources']->extend('africastalking', AfricasTalkingSource::class);

        $this->app['datasources']->extend('mteja', MtejaSource::class);
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

    public function registerFeatures()
    {
        $this->app->singleton('features', function ($app) {
            return new Features($app[ConfigRepository::class]);
        });

        // Register OhanzeeResolver
        $this->app->singleton(OhanzeeResolver::class, function ($app) {
            return new OhanzeeResolver();
        });

        $this->app->bind(CountryCodeRepository::class, EloquentCountryCodeRepository::class);
        $this->app->bind(
            User\UserRepository::class,
            User\EloquentUserRepository::class
        );
        $this->app->bind(
            user\UserSettingRepository::class,
            user\EloquentUserSettingRepository::class
        );
        $this->app->bind(PermissionsRepository::class, EloquentPermissionsRepository::class);
        $this->app->bind(RoleRepository::class, EloquentRoleRepository::class);
        $this->app->bind(TosRepository::class, EloquentTosRepository::class);
    }
}
