<?php

// Web hooks
$router->group([
    'middleware' => ['auth:api', 'scope:webhooks'],
], function () use ($router) {
    resource($router, 'webhooks', 'WebhooksController');
    $router->put('/webhooks/posts', 'WebhookPostsController@update');
});
