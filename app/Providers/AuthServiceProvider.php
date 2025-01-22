<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];


        /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Skip migrations ... run with phinx
        Passport::ignoreMigrations();

        // Set token expiries
        Passport::tokensExpireIn(Carbon::now()->addHours(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(7));

        // Register routes
        $this->passportRoutes();
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        // $this->app['auth']->viaRequest('api', function ($request) {
        //     if ($request->input('api_token')) {
        //         return User::where('api_token', $request->input('api_token'))->first();
        //     }
        // });

        // Set passport key path
        Passport::loadKeysFrom(storage_path('passport/'));

        // Define passport scopes
        $this->defineScopes();
        // need to use a string here or laravel goes wild and doesn't authorize anything
    }

    protected function defineScopes()
    {
        // Define possible scopes
        // @todo simplify / improve these
        Passport::tokensCan([
            'api' => 'Access API',
            'apikeys' => 'Access API keys',
            'country_codes' => 'Access Country Codes',
            'posts' => 'Access posts',
            'forms' => 'Access forms',
            'sets' => 'Access sets',
            'tags' => 'Access tags',
            'tos' => 'Access TOS',
            'users' => 'Access users',
            'media' => 'Access media',
            'config' => 'Access config',
            'messages' => 'Access messages',
            'dataproviders' => 'Access dataproviders',
            'stats' => 'Access stats',
            'layers' => 'Access layers',
            'savedsearches' => 'Access savedsearches',
            'notifications' => 'Access notifications',
            'contacts' => 'Access contacts',
            'csv' => 'Access csv',
            'roles' => 'Access roles',
            'permissions' => 'Access permissions',
            'migrate' => 'Access migrate',
            'webhooks' => 'Access webhooks',
            'hxl' => 'Access HDX & HXL features',
        ]);
    }

    protected function passportRoutes($callback = null, array $options = [])
    {
        $this->app->router->group([
            'prefix' => 'oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ], function ($router) {
            $router->post('/token', [
                'uses' => 'AccessTokenController@issueToken',
            ]);
        });
    }
}
