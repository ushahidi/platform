<?php

// Forms
$router->group([
    'namespace' => 'Forms',
    'prefix' => 'forms',
    'middleware' => ['scope:forms', 'expiration']
], function () use ($router) {
    // Public access
    $router->get('/', 'FormsController@index');
    $router->get('/{id:[0-9]+}', 'FormsController@show');

    $router->get('/attributes', 'AttributesController@index');
    $router->get('/stages', 'StagesController@index');

    // Sub-form routes
    $router->group(['prefix' => '/{form_id:[0-9]+}'], function () use ($router) {
        // Attributes
        $router->group(['prefix' => 'attributes'], function () use ($router) {
            $router->get('/', 'AttributesController@index');
            $router->get('/{id}', 'AttributesController@show');
        });

        // Contacts
        $router->group(['prefix' => 'contacts'], function () use ($router) {
            $router->get('/', 'ContactsController@index');
            $router->get('/{id}', 'ContactsController@show');
            $router->post('/', 'ContactsController@store');
            $router->put('/{id}', 'ContactsController@update');
            $router->delete('/{id}', 'ContactsController@destroy');
        });

        // Stages
        $router->group(['prefix' => 'stages'], function () use ($router) {
            $router->get('/', 'StagesController@index');
            $router->get('/{id}', 'StagesController@show');
        });

        // Stats
        $router->group(['prefix' => 'stats'], function () use ($router) {
            $router->get('/', 'StatsController@index');
        });

        // Roles
        $router->group(['prefix' => 'roles'], function () use ($router) {
            $router->get('/', 'RolesController@index');
        });
    });

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:forms']
    ], function () use ($router) {
        $router->post('/', 'FormsController@store');
        $router->put('/{id:[0-9]+}', 'FormsController@update');
        $router->delete('/{id:[0-9]+}', 'FormsController@destroy');

        // Sub-form routes
        $router->group(['prefix' => '/{form_id:[0-9]+}'], function () use ($router) {
            // Attributes
            $router->group(['prefix' => 'attributes'], function () use ($router) {
                $router->post('/', 'AttributesController@store');
                $router->put('/{id}', 'AttributesController@update');
                $router->delete('/{id}', 'AttributesController@destroy');
            });

            // Stages
            $router->group(['prefix' => 'stages'], function () use ($router) {
                $router->post('/', 'StagesController@store');
                $router->put('/{id}', 'StagesController@update');
                $router->delete('/{id}', 'StagesController@destroy');
            });

            // Roles
            $router->group(['prefix' => 'roles'], function () use ($router) {
                $router->put('/', 'RolesController@replace');
            });
        });
    });
});
