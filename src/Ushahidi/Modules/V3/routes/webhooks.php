<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Web hooks
$router->resource('webhooks', 'WebhooksController', [
    'middleware' => ['auth:api', 'scope:webhooks', 'expiration'],
    'parameters' => ['webhooks' => 'id'],
]);

// Webhook posts update endpoint
$router->put('/webhooks/posts/{id}', [
    'uses' => 'WebhookPostsController@update',
    'middleware' => ['signature'],
    'where' => ['id' => '[0-9]+'],
]);
