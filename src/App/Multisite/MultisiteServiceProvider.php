<?php

namespace Ushahidi\App\Multisite;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class MultisiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register manager
        $this->app->singleton('multisite', function ($app) {
            return new MultisiteManager(config('multisite'), $app[SiteRepository::class]);
        });

        // Register OhanzeeResolver
        $this->app->singleton(OhanzeeResolver::class, function ($app) {
            return new OhanzeeResolver();
        });
    }

    // @todo move some of this into manager?
    public function boot()
    {
        // Set up listeners
        Event::listen('multisite.site.changed', function ($site) {
            Log::debug('Handling multisite.site.change', [$site]);
            $dbConfig = $site->getDbConfig();
            $connectionName = 'deployment-'.$site->getId();
            $this->app->make(OhanzeeResolver::class)->setConnection($connectionName, $dbConfig);

            // @todo save db config into config
            $defaults = config('database.connections.mysql'); // @todo use actual default config
            config(['database.connections.'.$connectionName => $dbConfig + $defaults]);
            $this->app->make(ConnectionResolverInterface::class)->setDefaultConnection($connectionName);
        });

        $multisite = $this->app->make('multisite');

        // If multisite is disabled, use the default connection
        if (!$multisite->enabled()) {
            Log::debug('Multisite disabled, setting up default site');
            $multisite->setDefaultSite();
        }

        // If we're running at CLI and HOST variable envvar is set
        if ($multisite->enabled() && $this->app->runningInConsole()) {
            if ($host = env('HOST', false)) {
                // ... try to set site from HOST
                $multisite->setSiteFromHost($host);
            } else {
                $multisite->setDefaultSite();
            }
        }
    }
}
