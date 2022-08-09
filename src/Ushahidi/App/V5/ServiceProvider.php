<?php

namespace Ushahidi\App\V5;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\AggregateServiceProvider;

class ServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array
     */
    protected $providers = [
        Providers\AuthServiceProvider::class,
        Providers\EventServiceProvider::class,
        Providers\MorphServiceProvider::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace('Ushahidi\App\V5\Http\Controllers')
            ->group(__DIR__ . '/routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }
}
