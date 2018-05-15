<?php

// Users
$router->group([
    'middleware' => ['scope:users']
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
});
