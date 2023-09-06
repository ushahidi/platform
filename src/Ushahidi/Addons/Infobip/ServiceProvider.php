<?php

namespace Ushahidi\Addons\Infobip;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ushahidi\Addons\Infobip\InfobipSMS\InfobipSMS;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['datasources']->extend('infobip-sms', InfobipSMS::class);
    }
}
