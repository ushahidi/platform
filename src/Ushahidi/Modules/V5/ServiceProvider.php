<?php

namespace Ushahidi\Modules\V5;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\AggregateServiceProvider;
use Ushahidi\Modules\V5\Http\Middleware\V5GlobalScopes;

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
        $this->app[Kernel::class]->pushMiddleware(V5GlobalScopes::class);

        Route::prefix('api')
            ->middleware('api')
            ->namespace('Ushahidi\Modules\V5\Http\Controllers')
            ->group(__DIR__ . '/routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        RepositoryService::repositoryBinderResolver(function () {
            //
        });

        parent::register();
    }
}
