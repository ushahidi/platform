<?php

namespace Ushahidi\Multisite;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Ushahidi\Multisite\Middleware\DetectSite;
use Ushahidi\Multisite\Middleware\MaintenanceMode;
use Ushahidi\Multisite\Middleware\CheckDemoExpiration;

class MultisiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register manager
        $this->app->singleton('multisite', function ($app) {
            return new MultisiteManager(
                $this->app['config']['multisite'],
                $app[SiteRepository::class],
                $app[\Illuminate\Contracts\Events\Dispatcher::class]
            );
        });

        $this->app->alias('multisite', MultisiteManager::class);
    }

    // @todo move some of this into manager?
    public function boot()
    {
        $this->app[Kernel::class]->pushMiddleware(DetectSite::class);
        $this->app[Kernel::class]->pushMiddleware(MaintenanceMode::class);

        $this->app['router']->aliasMiddleware('expiration', CheckDemoExpiration::class);

        $this->setupListeners();

        $this->setupSite();
    }

    protected function setupSite()
    {
        $multisite = $this->app->make('multisite');

        // If multisite is disabled, use the default connection
        if (!$multisite->enabled()) {
            // Log::debug('Multisite disabled, setting up default site');
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

    protected function setupListeners()
    {
        Event::listen('site.restored', function ($site) {
            if (isset($site)) {
                $this->app['multisite']->setSiteById($site);
            } else {
                $this->app['multisite']->setDefaultSite();
            }
        });

        Event::listen('site.changed', function (Site $site) {
            // Log::debug('Handling multisite.site.change', [$site]);
            $dbConfig = $site->getDbConfig();
            $connectionName = 'deployment-'.$site->getId();

            $this->app->make(OhanzeeResolver::class)->setConnection($connectionName, $dbConfig);

            $default = Config::get('database.default');
            Config::set(
                'database.connections.'.$connectionName,
                array_merge(Config::get("database.connections.{$default}"), $dbConfig)
            );

            $this->app['db']->setDefaultConnection($connectionName);
            Config::set('passport.storage.database.connection', $connectionName);

            // Set cache prefix
            if (method_exists(Cache::store()->getStore(), 'setPrefix')) {
                Cache::setPrefix($connectionName);
            }
        });
    }
}
