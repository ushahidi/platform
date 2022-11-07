<?php

namespace App\Providers;

use Ushahidi\Addons\Mteja\MtejaSource;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Core\Tool\FeaturesManager;
use Ushahidi\Core\Tool\SiteManager;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Ushahidi\Addons\AfricasTalking\AfricasTalkingSource;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;

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
        $this->app['datasources']->extend('africastalking', function ($config) {
            return new AfricasTalkingSource($config);
        });

        $this->app['datasources']->extend('mteja', function ($config) {
            return new MtejaSource($config);
        });

        $this->app['datasources']->registerRoutes($this->app->router);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('site', function ($app, $params) {
            return new SiteManager(
                $app[ConfigRepository::class],
                $params ? $params['cache_lifetime'] : null
            );
        });

        $this->app['events']->listen('site.changed', function ($site) {
            $this->app['site']->setDefault($site);
        });

        $this->app->bind('features', function ($app) {
            return new FeaturesManager($app[ConfigRepository::class]);
        });

        // Register OhanzeeResolver
        $this->app->singleton(OhanzeeResolver::class, function ($app) {
            return new OhanzeeResolver();
        });
    }
}
