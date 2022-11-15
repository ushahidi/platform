<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Permissions
$router->resource('permissions', 'PermissionsController', [
    'middleware' => ['auth:api', 'scope:permissions', 'expiration'],
    'only' => ['index', 'show'],
    'parameters' => ['permissions' => 'id'],
]);
