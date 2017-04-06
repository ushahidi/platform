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
    }
}
