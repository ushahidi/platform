<?php

// Tags
$router->group([
    'prefix' => 'tags',
    'middleware' => ['scope:tags', 'expiration']
], function () use ($router) {
    // Public access
    resource($router, '/', 'TagsController', [
        'only' => ['index', 'show'],
    ]);

    // Restricted access
    resource($router, '/', 'TagsController', [
        'middleware' => ['auth:api'],
        'only' => ['store', 'update', 'destroy'],
    ]);
});
