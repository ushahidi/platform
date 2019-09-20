<?php

// Users
$router->group([
    'namespace' => 'Users',
    'middleware' => ['scope:users', 'expiration']
], function () use ($router) {
    // Public access
    resource($router, 'users', 'UsersController', [
        'only' => ['index', 'show']
    ]);

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:users']
    ], function () use ($router) {
        resource($router, 'users', 'UsersController', [
            'only' => ['store', 'update', 'destroy']
        ]);
        $router->get('/users/me', 'UsersController@showMe');
        $router->put('/users/me', 'UsersController@updateMe');
    });

    // Sub-user routes
    // User settings
    resource($router, 'users/{user_id:[0-9]+}/settings', 'SettingsController', [
        'middleware' => ['feature:user-settings']
    ]);
});
