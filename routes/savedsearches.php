<?php

// Saved Searches
$router->group([
    'prefix' => 'savedsearches',
    'middleware' => ['scope:savedsearches']
], function () use ($router) {
    // Public access
    $router->get('/', 'SavedSearchesController@index');
    $router->get('/{id}', 'SavedSearchesController@show');

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:savedsearches']
    ], function () use ($router) {
        $router->post('/', 'SavedSearchesController@store');
        $router->put('/{id}', 'SavedSearchesController@update');
        $router->delete('/{id}', 'SavedSearchesController@destroy');
    });
});
