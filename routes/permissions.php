<?php

// Permissions
$router->group([
    'middleware' => ['auth:api', 'scope:permissions'],
    'prefix' => 'permissions'
], function () use ($router) {
    $router->get('/', 'PermissionsController@index');
    $router->get('/{id}', 'PermissionsController@show');
});
