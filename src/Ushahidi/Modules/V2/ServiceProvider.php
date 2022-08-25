<?php

namespace Ushahidi\Modules\V2;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            Contracts\ImportMappingRepository::class,
            Repositories\ImportMappingRepository::class
        );

        $this->app->singleton(
            Contracts\ImportRepository::class,
            Repositories\ImportRepository::class
        );

        $this->app->singleton(
            Contracts\ImportSourceDataRepository::class,
            Repositories\ImportSourceDataRepository::class
        );

        $this->app->singleton(
            Contracts\ImportDataTools::class,
            Utils\ImportDataTools::class
        );
    }
}
