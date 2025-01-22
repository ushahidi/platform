<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Roles
$router->group([
    'middleware' => ['scope:roles', 'expiration'],
], function () use ($router) {
    // Public access
    $router->resource('roles', 'RolesController', [
        'only' => ['index', 'show'],
        'parameters' => ['roles' => 'id'],
    ]);

    // Restricted access
    $router->resource('roles', 'RolesController', [
        'middleware' => ['auth:api'],
        'only' => ['store', 'update', 'destroy'],
        'parameters' => ['roles' => 'id'],
    ]);
});
