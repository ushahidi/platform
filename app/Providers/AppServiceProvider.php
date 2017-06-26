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
        $this->app->singleton(\Ushahidi\Factory\UsecaseFactory::class, function ($app) {
            // Just return it from AuraDI
            return service('factory.usecase');
        });

        $this->app->singleton(\Ushahidi\Core\Entity\MessageRepository::class, function ($app) {
            // Just return it from AuraDI
            return service('repository.message');
        });

        $this->app->configure('cdn');
        $this->app->configure('ratelimiter');
        $this->app->configure('multisite');
        $this->app->configure('ohanzee-db');

        // Add filesystem
        $this->app->singleton('filesystem', function ($app) {
            return $app->loadComponent(
                'filesystems',
                \Illuminate\Filesystem\FilesystemServiceProvider::class,
                'filesystem'
            );
        });

        // Add mailer
        $this->app->singleton('mailer', function ($app) {
            return $app->loadComponent(
                'mail',
                \Illuminate\Mail\MailServiceProvider::class,
                'mailer'
            );
        });


        $this->app->singleton('datasources', function () {
            return $this->app->loadComponent(
                'datasources',
                \Ushahidi\App\DataSource\DataSourceServiceProvider::class,
                'datasources'
            );
        });

        $this->configureAuraDI();

        // Hack, must construct it to register route :/
        $this->app->make('datasources');
    }

    // @todo move most of this elsewhere
    protected function configureAuraDI()
    {
        $di = service();

        // Multisite db
        $di->set('site', function () use ($di) {
            $site = '';

            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                $site = $di->get('multisite')->getSite();
            }

            return $site;
        });

        // Site config
        $di->set('site.config', function () use ($di) {
            return $di->get('repository.config')->get('site')->asArray();
        });

        // Client Url
        $di->set('clienturl', function () use ($di) {
            return $this->getClientUrl($di->get('site.config'));
        });

        // Feature config
        $di->set('features', function () use ($di) {
            return $di->get('repository.config')->get('features')->asArray();
        });

        // Roles config settings
        $di->set('roles.enabled', function () use ($di) {
            $config = $di->get('features');

            return $config['roles']['enabled'];
        });

        // Feature config
        $di->set('features.limits', function () use ($di) {
            $config = $di->get('features');

            return $config['limits'];
        });

        // Webhooks config settings
        $di->set('webhooks.enabled', function () use ($di) {
            $config = $di->get('features');

            return $config['webhooks']['enabled'];
        });

        // Post Locking config settings
        $di->set('post-locking.enabled', function () use ($di) {
            $config = $di->get('features');

            return $config['post-locking']['enabled'];
        });

        // Redis config settings
        $di->set('redis.enabled', function () use ($di) {
            $config = $di->get('features');

            return $config['redis']['enabled'];
        });

        // Data import config settings
        $di->set('data-import.enabled', function () use ($di) {
            $config = $di->get('features');

            return $config['data-import']['enabled'];
        });

        $di->set('features.data-providers', function () use ($di) {
            $config = $di->get('features');

            return array_filter($config['data-providers']);
        });

        // CDN Config settings
        $di->set('cdn.config', function () use ($di) {
            return config('cdn');
        });

        // Ratelimiter config settings
        $di->set('ratelimiter.config', function () use ($di) {
            return config('ratelimiter');
        });

        // Private deployment config settings
        // @todo move to repo
        $di->set('site.private', function () use ($di) {
            $site = $di->get('site.config');
            $features = $di->get('features');
            return $site['private']
                and $features['private']['enabled'];
        });

        $di->set('tool.uploader.prefix', function () use ($di) {
            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                return $di->get('multisite')->getCdnPrefix();
            }

            return '';
        });

        // Configure mailer
        $di->set('tool.mailer', $di->lazyNew('Ushahidi\App\Tools\LumenMailer', [
            'mailer' => app('mailer'),
            'siteConfig' => $di->lazyGet('site.config'),
            'clientUrl' => $di->lazyGet('clienturl')
        ]));

        // @todo move to auth provider?
        $di->set('session', $di->lazyNew(\Ushahidi\App\Tools\LumenSession::class, [
            'userRepo' => $di->lazyGet('repository.user')
        ]));

        // Kohana injection
        // DB config
        $di->set('db.config', function() use ($di) {
            $config = config('ohanzee-db');
            $config = $config['default'];

            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                $config = $di->get('multisite')->getDbConfig();
            }

            return $config;
        });
        // Multisite db
        $di->set('kohana.db.multisite', function () use ($di) {
            $config = config('ohanzee-db');

            return \Ohanzee\Database::instance('multisite', $config['default']);
        });
        // Deployment db
        $di->set('kohana.db', function() use ($di) {
            return \Ohanzee\Database::instance('deployment', $di->get('db.config'));
        });

        // Intercom config settings
        $di->set('site.intercomAppToken', function() use ($di) {
            // FIXME
            return false;
        });
    }

    protected function getClientUrl($config)
    {
        $clientUrl = env('CLIENT_URL', false);

        if (env("MULTISITE_DOMAIN", false)) {
            try {
                $host = \League\Url\Url::createFromServer($_SERVER)->getHost()->toUnicode();
                $clientUrl = str_replace(env("MULTISITE_DOMAIN"), env("MULTISITE_CLIENT_DOMAIN"), $host);
            } catch (Exception $e) {
            }
        }

        // Or overwrite from config
        if (!$clientUrl && $config['client_url']) {
            $client_url = $config['client_url'];
        }

        return $clientUrl;
    }
}
