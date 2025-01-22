<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Saved Searches
$router->group([
    'middleware' => ['scope:savedsearches', 'expiration'],
], function () use ($router) {
    // Public access
    $router->resource('savedsearches', 'SavedSearchesController', [
        'only' => ['index', 'show'],
        'parameters' => ['savedsearches' => 'id'],
    ]);

    // Restricted access
    $router->resource('savedsearches', 'SavedSearchesController', [
        'middleware' => ['auth:api'],
        'only' => ['store', 'update', 'destroy'],
        'parameters' => ['savedsearches' => 'id'],
    ]);
});
