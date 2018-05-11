<?php

// Collections
$router->group([
        'namespace' => 'Collections',
        'prefix' => 'collections',
        'middleware' => ['scope:collections,sets']
], function () use ($router) {
    // Public access
    $router->get('/', 'CollectionsController@index');
    $router->get('/{id}', 'CollectionsController@show');
    $router->group(['prefix' => '/{set_id:[0-9]+}/posts'], function () use ($router) {
        $router->get('/', 'PostsController@index');
        $router->get('/{id}', 'PostsController@show');
    });

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:collections,sets']
    ], function () use ($router) {
        $router->post('/', 'CollectionsController@store');
        $router->put('/{id}', 'CollectionsController@update');
        $router->delete('/{id}', 'CollectionsController@destroy');

        $router->group(['prefix' => '/{set_id:[0-9]+}/posts'], function () use ($router) {
            $router->post('/', 'PostsController@store');
            //$router->put('/{id}', 'PostsController@update');
            $router->delete('/{id}', 'PostsController@destroy');
        });
    });
});
