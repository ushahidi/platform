<?php

namespace App\Providers;

use App\Tools\Mailer;
use App\Tools\Session;
use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Illuminate\Support\Facades\Storage;
use Ushahidi\Multisite\OhanzeeResolver;

class LumenAuraConfig extends ContainerConfig
{
    /**
     * Define params, setters, and services before the Container is locked.
     *
     * @param Container $di The DI container.
     */
    public function define(Container $di): void
    {
        $this->configureAuraServices($di);
        $this->injectAuraConfig($di);
    }

    protected function configureAuraServices(Container $di)
    {
        // Configure mailer
        $di->set('tool.mailer', $di->lazyNew(Mailer::class, [
            'mailer' => app('mailer'),
        ]));

        // Configure filesystem
        // The Ushahidi filesystem adapter returns a flysystem adapter for a given
        // cdn type based on the provided configuration
        $di->set('tool.filesystem', function () {
            // Get the underlying League\Flysystem\Filesystem instance
            return Storage::disk()->getDriver();
        });

        $di->set('multisite', function () {
            return app('multisite');
        });

        // Setup user session service
        $di->set('session', $di->lazyNew(Session::class, [
            'userRepo' => $di->lazyGet('repository.user'),
        ]));

        $di->set('db.eloquent.resolver', $di->lazy(function () {
            return app('db');
        }));

        // Abstract repository parameters
        $di->set('db.ohanzee.resolver', $di->lazy(function () {
            return app(\Ushahidi\Core\Tool\OhanzeeResolver::class);
        }));

        // Configure dispatcher
        $di->setters[\Ushahidi\Core\Concerns\DispatchesEvents::class]
        ['setDispatcher'] = app('events');
    }

    protected function injectAuraConfig(Container $di)
    {
        // CDN Config settings
        $di->values['cdn.config'] = config('cdn');

        // $di->set('cdn.config', function () {
        //     return config('cdn');
        // });

        // Ratelimiter config settings
        $di->values['ratelimiter.config'] = config('ratelimiter');

        // $di->set('ratelimiter.config', function () use ($di) {
        //     return config('ratelimiter');
        // });
    }
}
