<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Layers
$router->group([
    'middleware' => ['scope:layers', 'expiration'],
], function () use ($router) {
    // Public access
    $router->resource('layers', 'LayersController', [
        'only' => ['index', 'show'],
        'parameters' => ['layers' => 'id'],
    ]);

    // Restricted access
    $router->resource('layers', 'LayersController', [
        'only' => ['store', 'update', 'destroy'],
        'middleware' => ['auth:api'],
        'parameters' => ['layers' => 'id'],
    ]);
});
