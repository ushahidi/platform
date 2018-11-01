<?php

namespace Ushahidi\App\Multisite;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\ConnectionResolverInterface;

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

    public function boot()
    {
        // If we're running at CLI and HOST variable envvar is set
        if ($this->app->runningInConsole() && $host = env('HOST', false)) {
            // ... try to set site from HOST
            $this->app->make('multisite')->setSiteFromHost($host);
        }
        // @todo check multisite enabled and site set from somewhere??
    }
}
