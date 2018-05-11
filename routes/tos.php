<?php

// TOS
$router->group([
    'middleware' => ['auth:api', 'scope:tos'],
    'prefix' => 'tos'
], function () use ($router) {
    $router->get('/', 'TosController@index');
    $router->post('/', 'TosController@store');
    $router->get('/{id}', 'TosController@show');
    //$router->put('/{id}', 'TosController@update');
    //$router->delete('/{id}', 'TosController@destroy');
});
