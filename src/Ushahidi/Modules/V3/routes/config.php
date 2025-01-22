<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Config
$router->group([
    'middleware' => ['scope:config'],
], function () use ($router) {
    // @todo stop using this in client, and remove?
    $router->options('/config', ['uses' => 'ConfigController@indexOptions']);

    // Public access
    $router->resource('/config', 'ConfigController', [
        'only' => ['index', 'show'],
        'parameters' => ['config' => 'id'],
    ]);

    // Restricted access
    $router->put('/config/{id}', [
        'uses' => 'ConfigController@update'
    ])->middleware('auth:api', 'expiration');
});
