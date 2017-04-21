<?php

namespace Ushahidi\App\Providers;

use Ushahidi\App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Ushahidi\App\Auth\UshahidiUserProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
        //
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

        $this->app['auth']->provider('ushahidi', function ($app, array $config) {
            return new UshahidiUserProvider(service('user.repo'));
        });

        Passport::loadKeysFrom(storage_path('passport/'));
        // Define possible scopes
        // @todo simplify / improve these
        Passport::tokensCan([
            'api' => 'Access API',
            'posts' => 'Access posts',
            'forms' => 'Access forms',
            'sets' => 'Access sets',
            'tags' => 'Access tags',
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
            'webhooks' => 'Access webhooks'
        ]);

    }
}
