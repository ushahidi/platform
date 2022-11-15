<?php
/**
 *  @var $router \Illuminate\Routing\Router
 */

// Collections
$router->group([
    'namespace' => 'Collections',
    'middleware' => ['scope:collections,sets', 'expiration'],
], function () use ($router) {
    // Public access
    $router->resource('collections', 'CollectionsController', [
        'only' => ['index', 'show'],
        'parameters' => ['collections' => 'id'],
    ]);

    // Restricted access
    $router->resource('collections', 'CollectionsController', [
        'only' => ['store', 'update', 'destroy'],
        'middleware' => ['auth:api'],
        'parameters' => ['collections' => 'id'],
    ]);

    $router->group([
        'prefix' => '/collections/{set_id}/posts',
        'where' => ['set_id' => '[0-9]+'],
    ], function () use ($router) {
        $router->get('/', 'PostsController@index');
        $router->get('/{id}', 'PostsController@show');
        $router->post('/', 'PostsController@store')->middleware('auth:api');
        //$router->put('/{id}', 'PostsController@update')->middleware('auth:api');
        $router->delete('/{id}', 'PostsController@destroy')->middleware('auth:api');
    });
});
