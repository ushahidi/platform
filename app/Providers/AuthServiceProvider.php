<?php

namespace Ushahidi\App\Providers;

use Ushahidi\App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Ushahidi\App\Auth\UshahidiUserProvider;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use Illuminate\Database\Connection;

class AuthServiceProvider extends ServiceProvider
{
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
        Passport::tokensExpireIn(Carbon::now()->addDays(1));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(7));

        // Register routes
        $this->passportRoutes();

        // Provide connection class binding for Passport
        $this->app->singleton(Connection::class, function () {
            return $this->app['db.connection'];
        });
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
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
            'hxl' => 'Access HDX & HXL features'
        ]);
    }

    protected function passportRoutes($callback = null, array $options = [])
    {
        $this->app->router->group([
            'prefix' => 'oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ], function ($router) {
            $router->post('/token', [
                'uses' => 'AccessTokenController@issueToken'
            ]);
        });
    }
}
