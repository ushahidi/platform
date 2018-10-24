<?php

// Saved Searches
$router->group([
    'prefix' => 'savedsearches',
    'middleware' => ['scope:savedsearches', 'expiration']
], function () use ($router) {
    // Public access
    resource($router, '/', 'SavedSearchesController', [
        'only' => ['index', 'show'],
    ]);

    // Restricted access
    resource($router, '/', 'SavedSearchesController', [
        'middleware' => ['auth:api'],
        'only' => ['store', 'update', 'destroy'],
    ]);
});
