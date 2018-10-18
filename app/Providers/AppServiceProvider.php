<?php

namespace Ushahidi\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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

        $this->configureAuraDI();

        $this->registerDataSources();

        $this->setupMultisiteIlluminateDB();

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

    public function registerDataSources()
    {
        $this->app->register(\Ushahidi\App\DataSource\DataSourceServiceProvider::class);
    }

    protected function configureAuraDI()
    {
        $di = service();

        $this->configureAuraServices($di);
        $this->injectAuraConfig($di);
    }

    protected function configureAuraServices(\Aura\Di\ContainerInterface $di)
    {
        // Configure mailer
        $di->set('tool.mailer', $di->lazyNew('Ushahidi\App\Tools\LumenMailer', [
            'mailer' => app('mailer'),
            'siteConfig' => $di->lazyGet('site.config'),
            'clientUrl' => $di->lazyGet('clienturl')
        ]));

        // Configure filesystem
        // The Ushahidi filesystem adapter returns a flysystem adapter for a given
        // cdn type based on the provided configuration
        $di->set('tool.filesystem', function () {
            // Get the underlying League\Flysystem\Filesystem instance
            return $this->app->make('filesystem')->disk()->getDriver();
        });

        // Setup user session service
        $di->set('session', $di->lazyNew(\Ushahidi\App\Tools\LumenSession::class, [
            'userRepo' => $di->lazyGet('repository.user')
        ]));

        // Multisite db
        $di->set('kohana.db.multisite', function () use ($di) {
            $config = config('ohanzee-db');

            return \Ohanzee\Database::instance('multisite', $config['multisite']);
        });

        // Deployment db
        $di->set('kohana.db', function () use ($di) {
            return \Ohanzee\Database::instance('deployment', $this->getDbConfig($di));
        });

        $di->set('db.eloquent.connection', function () use ($di) {
            return DB::connection();
        });

        // Configure dispatcher
        $di->setter[\Ushahidi\Core\Traits\Events\DispatchesEvents::class]['setDispatcher'] = $this->app->make('events');
    }

    protected function injectAuraConfig(\Aura\Di\ContainerInterface $di)
    {
        // CDN Config settings
        $di->set('cdn.config', function () use ($di) {
            return config('cdn');
        });

        // Ratelimiter config settings
        $di->set('ratelimiter.config', function () use ($di) {
            return config('ratelimiter');
        });

        // Multisite db
        // Move multisite enabled check to class and move to src/App
        $di->set('site', function () use ($di) {
            // @todo default to using the current domain
            $site = '';

            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                $site = $di->get('multisite')->getSite();
            }

            return $site;
        });

        // Move multisite enabled check to class and move to src/App
        $di->set('tool.uploader.prefix', function () use ($di) {
            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                return $di->get('multisite')->getCdnPrefix();
            }

            return '';
        });

        // Client Url
        $di->set('clienturl', function () use ($di) {
            return $this->getClientUrl($di->get('site.config'), $di->lazyGet('multisite'));
        });
    }

    protected function getDbConfig(\Aura\Di\ContainerInterface $di)
    {
        // Kohana injection
        // DB config
        $config = config('ohanzee-db');
        $config = $config['default'];

        // Is this a multisite install?
        $multisite = config('multisite.enabled');
        if ($multisite) {
            $config = $di->get('multisite')->getDbConfig();
        }

        return $config;
    }

    protected function getClientUrl($config, $multisite)
    {
        $clientUrl = env('CLIENT_URL', false);

        if (env("MULTISITE_DOMAIN", false)) {
            try {
                $clientUrl = $multisite()->getClientUrl();
            } catch (Exception $e) {
            }
        }

        // Or overwrite from config
        if (!$clientUrl && $config['client_url']) {
            $client_url = $config['client_url'];
        }

        return $clientUrl;
    }

    protected function setupMultisiteIlluminateDB()
    {
        $di = service();
        $config = $this->getDbConfig($di);

        $existing = config('database.connections.mysql');

        config(['database.connections.mysql' => [
            'database'  => $config['connection']['database'],
            'username'  => $config['connection']['username'],
            'password'  => $config['connection']['password'],
            'host'      => $config['connection']['hostname'],
        ] + $existing]);
    }

    public function registerFeatures()
    {
        $this->app->singleton('features', function ($app) {
            return new \Ushahidi\App\Tools\Features(service('features'));
        });
    }
}
