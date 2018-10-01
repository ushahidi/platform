<?php

// Web hooks
resource($router, 'webhooks', 'WebhooksController', [
    'middleware' => ['auth:api', 'scope:webhooks', 'expiration']
]);

// Webhook posts update endpoint
$router->put('/webhooks/posts/{id:[0-9]+}', [
    'uses' => 'WebhookPostsController@update',
    'middleware' => ['signature']
]);
