<?php

// Media
$router->group([
    'prefix' => 'media',
    'middleware' => ['scope:media', 'expiration']
], function () use ($router) {
    // Public access
    $router->get('/', 'MediaController@index');
    $router->get('/{id}', 'MediaController@show');
    // Public can upload media
    $router->post('/', 'MediaController@store');

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:media']
    ], function () use ($router) {
        $router->put('/{id}', 'MediaController@update');
        $router->delete('/{id}', 'MediaController@destroy');
    });
});
