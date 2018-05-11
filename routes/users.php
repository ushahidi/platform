<?php

// Users
$router->group([
    'prefix' => 'users',
    'middleware' => ['scope:users']
], function () use ($router) {
    // Public access
    $router->get('/', 'UsersController@index');
    $router->get('/{id:[0-9]+}', 'UsersController@show');

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:users']
    ], function () use ($router) {
        $router->post('/', 'UsersController@store');
        $router->put('/{id:[0-9]+}', 'UsersController@update');
        $router->delete('/{id:[0-9]+}', 'UsersController@destroy');
        $router->get('/me', 'UsersController@showMe');
        $router->put('/me', 'UsersController@updateMe');
    });
});
