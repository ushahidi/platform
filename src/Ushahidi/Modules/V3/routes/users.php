<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Users
$router->group([
    'namespace' => 'Users',
    'middleware' => ['scope:users', 'expiration'],
], function () use ($router) {

    $router->get('/users/me', 'UsersController@showMe')->middleware(['auth:api', 'scope:users']);
    $router->put('/users/me', 'UsersController@updateMe')->middleware(['auth:api', 'scope:users']);

    // Public access
    $router->resource('users', 'UsersController', [
        'only' => ['index', 'show'],
        'parameters' => ['users' => 'id'],
        'where' => ['id' => '[0-9]+'],
    ]);


    // Restricted access
    $router->resource('users', 'UsersController', [
        'only' => ['store', 'update', 'destroy'],
        'parameters' => ['users' => 'id'],
        'where' => ['id' => '[0-9]+'],
    ], [
        'middleware' => ['auth:api', 'scope:users'],
    ]);

    // Sub-user routes
    // User settings
    $router->resource('users/{user_id}/settings', 'SettingsController', [
        'middleware' => ['feature:user-settings'],
        'parameters' => ['settings' => 'id'],
        'where' => ['user_id' => '[0-9]+', 'id' => '[0-9]+'],
    ]);
});
