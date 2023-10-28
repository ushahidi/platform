<?php

namespace Ushahidi\Addons\Infobip;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class LaravelServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['datasources']->extend('infobip', InfobipServiceProvider::class);
    }
}
