<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Tags
$router->group([
    'middleware' => ['scope:tags', 'expiration'],
], function () use ($router) {
    // Public access
    $router->resource('tags', 'TagsController', [
        'only' => ['index', 'show'],
        'parameters' => ['tags' => 'id'],
    ]);

    // Restricted access
    $router->resource('tags', 'TagsController', [
        'only' => ['store', 'update', 'destroy'],
        'middleware' => ['auth:api'],
        'parameters' => ['tags' => 'id'],
    ]);
});
