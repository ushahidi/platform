<?php

// Roles
$router->group([
    'prefix' => 'roles',
    'middleware' => ['scope:roles', 'expiration']
], function () use ($router) {
    // Public access
    resource($router, '/', 'RolesController', [
        'only' => ['index', 'show'],
    ]);

    // Restricted access
    resource($router, '/', 'RolesController', [
        'middleware' => ['auth:api'],
        'only' => ['store', 'update', 'destroy'],
    ]);
});
