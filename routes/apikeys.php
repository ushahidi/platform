<?php

// API keys
$router->group([
    'middleware' => ['auth:api', 'scope:apikeys'],
    'prefix' => 'apikeys'
], function () use ($router) {
    $router->get('/', 'ApiKeysController@index');
    $router->post('/', 'ApiKeysController@store');
    $router->get('/{id}', 'ApiKeysController@show');
    $router->put('/{id}', 'ApiKeysController@update');
    $router->delete('/{id}', 'ApiKeysController@destroy');
});
