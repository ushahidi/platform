<?php

// Roles
$router->group([
    'prefix' => 'roles',
    'middleware' => ['scope:roles']
], function () use ($router) {
    // Public access
    $router->get('/', 'RolesController@index');
    $router->get('/{id}', 'RolesController@show');

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:roles']
    ], function () use ($router) {
        $router->post('/', 'RolesController@store');
        $router->put('/{id}', 'RolesController@update');
        $router->delete('/{id}', 'RolesController@destroy');
    });
});
