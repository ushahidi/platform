<?php

namespace Ushahidi\Addons\AfricasTalking;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['datasources']->extend('africastalking', AfricasTalkingSource::class);
    }
}
