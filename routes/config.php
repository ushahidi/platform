<?php

// Config
$router->group([
    'prefix' => 'config/',
    'middleware' => ['scope:config']
], function () use ($router) {
    // Public access
    $router->get('/', ['uses' => 'ConfigController@index']);
    // @todo stop using this in client, and remove?
    $router->options('/', ['uses' => 'ConfigController@indexOptions']);
    $router->get('/{id}', ['uses' => 'ConfigController@show']);

    // Restricted access
    $router->group(['middleware' => ['auth:api', 'scope:config']], function () use ($router) {
        // $router->post('/', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@store']);
        $router->put('/{id}', ['uses' => 'ConfigController@update']);
        // $router->delete('/{id}', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@destroy']);
    });
});
