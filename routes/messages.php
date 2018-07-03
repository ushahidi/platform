<?php

// Messages
$router->group([
    'middleware' => ['auth:api', 'scope:messages'],
    'prefix' => 'messages'
], function () use ($router) {
    $router->get('/', 'MessagesController@index');
    $router->post('/', 'MessagesController@store');
    $router->get('/{id}', 'MessagesController@show');
    $router->get('/{id}/post', 'MessagesController@showPost');
    $router->put('/{id}', 'MessagesController@update');
    // $router->delete('/{id}', 'MessagesController@destroy');
});
