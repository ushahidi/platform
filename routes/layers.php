<?php

// Layers
$router->group([
    'prefix' => 'layers',
    'middleware' => ['scope:layers']
], function () use ($router) {
    // Public access
    $router->get('/', 'LayersController@index');
    $router->get('/{id}', 'LayersController@show');

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:layers']
    ], function () use ($router) {
        $router->post('/', 'LayersController@store');
        $router->put('/{id}', 'LayersController@update');
        $router->delete('/{id}', 'LayersController@destroy');
    });
});
