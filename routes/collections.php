<?php

// Collections
$router->group([
        'namespace' => 'Collections',
        'prefix' => 'collections',
        'middleware' => ['scope:collections,sets', 'expiration']
], function () use ($router) {
    // Public access
    resource($router, '/', 'CollectionsController', [
        'only' => ['index', 'show']
    ]);
    $router->group(['prefix' => '/{set_id:[0-9]+}/posts'], function () use ($router) {
        $router->get('/', 'PostsController@index');
        $router->get('/{id}', 'PostsController@show');
    });

    // Restricted access
    $router->group([
        'middleware' => ['auth:api']
    ], function () use ($router) {
        resource($router, '/', 'CollectionsController', [
            'only' => ['store', 'update', 'destroy']
        ]);

        $router->group(['prefix' => '/{set_id:[0-9]+}/posts'], function () use ($router) {
            $router->post('/', 'PostsController@store');
            //$router->put('/{id}', 'PostsController@update');
            $router->delete('/{id}', 'PostsController@destroy');
        });
    });
});
