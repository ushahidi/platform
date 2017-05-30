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

        $this->configureAuraDI();
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
        $di->set('site.config', function() use ($di) {
            return $di->get('repository.config')->get('site')->asArray();
        });

        // Feature config
        $di->set('features', function() use ($di) {
            return $di->get('repository.config')->get('features')->asArray();
        });

        // Roles config settings
        $di->set('roles.enabled', function() use ($di) {
            $config = $di->get('features');

            return $config['roles']['enabled'];
        });

        // Feature config
        $di->set('features.limits', function() use ($di) {
            $config = $di->get('features');

            return $config['limits'];
        });

        // Webhooks config settings
        $di->set('webhooks.enabled', function() use ($di) {
            $config = $di->get('features');

            return $config['webhooks']['enabled'];
        });

        // Data import config settings
        $di->set('data-import.enabled', function() use ($di) {
            $config = $di->get('features');

            return $config['data-import']['enabled'];
        });

        $di->set('features.data-providers', function() {
            $config = $di->get('features');

            return array_filter($config['data-providers']);
        });

        // CDN Config settings
        $di->set('cdn.config', function() use ($di) {
            return config('cdn');
        });

        // Ratelimiter config settings
        $di->set('ratelimiter.config', function() use ($di) {
            return config('ratelimiter');
        });

        // Private deployment config settings
        // @todo move to repo
        $di->set('site.private', function() use ($di) {
            $site = $di->get('site.config');
            $features = $di->get('features');
            return $config['private']
                and $features['private']['enabled'];
        });

        $di->set('tool.uploader.prefix', function() use ($di) {
            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                return $di->get('multisite')->getCdnPrefix();
            }

            return '';
        });

        // Multisite utility class
        $di->set('multisite', $di->lazyNew('Ushahidi\App\Multisite'));
        $di->params['Ushahidi\App\Multisite'] = [
            'db' => $di->lazyGet('kohana.db.multisite')
        ];

        // @todo move to auth provider?
        $di->set('session.user', function() use ($di) {
            // Using the OAuth resource server, get the userid (owner id) for this request
            // $server = $di->get('oauth.server.resource');
            // $userid = $server->getOwnerId();
            $genericUser = app('auth')->guard()->user();

            // Using the user repository, load the user
            $repo = $di->get('repository.user');
            $user = $repo->get($genericUser ? $genericUser->id : null);

            return $user;
        });
    }
}
