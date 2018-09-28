<?php

// Layers
$router->group([
    'prefix' => 'layers',
    'middleware' => ['scope:layers', 'expiration']
], function () use ($router) {
    // Public access
    resource($router, '/', 'LayersController', [
        'only' => ['index', 'show'],
    ]);

    // Restricted access
    resource($router, '/', 'LayersController', [
        'middleware' => ['auth:api'],
        'only' => ['store', 'update', 'destroy'],
    ]);
});
