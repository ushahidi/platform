<?php

// Config
$router->group([
    'prefix' => 'config/',
    'middleware' => ['scope:config']
], function () use ($router) {
    // Public access
    resource($router, '/', 'ConfigController', [
        'only' => ['index', 'show'],
        'id' => 'id' // Override id to allow non-numeric IDs
    ]);
    // @todo stop using this in client, and remove?
    $router->options('/', ['uses' => 'ConfigController@indexOptions']);

    // Restricted access
    $router->group(['middleware' => ['auth:api', 'scope:config', 'expiration']], function () use ($router) {
        $router->put('/{id}', ['uses' => 'ConfigController@update']);
    });
});
