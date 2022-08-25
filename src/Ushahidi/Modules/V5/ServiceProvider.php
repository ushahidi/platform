<?php

namespace Ushahidi\Modules\V5;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\AggregateServiceProvider;
use Ushahidi\Modules\V5\Repository\UserRepository as RepositoryUserRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;

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
        parent::register();

        $this->app->bind(UserRepository::class, RepositoryUserRepository::class);
    }
}
