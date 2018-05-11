<?php

// Web hooks
$router->group([
    'middleware' => ['auth:api', 'scope:webhooks'],
    'prefix' => 'webhooks'
], function () use ($router) {
    $router->get('/', 'WebhooksController@index');
    $router->post('/', 'WebhooksController@store');
    $router->get('/{id:[0-9]+}', 'WebhooksController@show');
    $router->put('/{id:[0-9]+}', 'WebhooksController@update');
    $router->delete('/{id:[0-9]+}', 'WebhooksController@destroy');

    $router->put('/posts', 'WebhookPostsController@update');
});
