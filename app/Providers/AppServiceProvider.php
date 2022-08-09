<?php

namespace App\Providers;

use Ushahidi\Core\Tool\Features;
use Ushahidi\Addons\Mteja\MtejaSource;
use Illuminate\Support\ServiceProvider;
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
        $this->app->singleton('features', function ($app) {
            return new Features($app[ConfigRepository::class]);
        });
    }
}
