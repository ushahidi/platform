<?php

// Posts
$router->group([
    'namespace' => 'Posts',
    'prefix' => 'posts',
    'middleware' => ['scope:posts', 'expiration']
], function () use ($router) {
    // Public access
    $router->get('/', 'PostsController@index');
    // @todo stop using this in client, and remove?
    $router->options('/', ['uses' => 'PostsController@indexOptions']);
    $router->get('/{id:[0-9]+}', 'PostsController@show');

    // GeoJSON
    $router->get('/geojson', 'GeoJSONController@index');
    $router->get('/geojson/{zoom}/{x}/{y}', 'GeoJSONController@index');
    $router->get('/{id:[0-9]+}/geojson', 'GeoJSONController@show');

    // Export
    $router->get('/export', 'ExportController@index');

    // Stats
    $router->get('/stats', 'PostsController@stats');

    // Sub-post routes
    $router->group(['prefix' => '/{parent_id:[0-9]+}'], function () use ($router) {
        // Revisions
        $router->group(['prefix' => 'revisions'], function () use ($router) {
            $router->get('/', 'RevisionsController@index');
            $router->get('/{id}', 'RevisionsController@show');
        });

        // Translations
        $router->group(['prefix' => 'translations'], function () use ($router) {
            $router->get('/', 'TranslationsController@index');
            $router->get('/{id:[0-9]+}', 'TranslationsController@show');
            $router->get('/{locale:[A-Za-z_]+}', 'TranslationsController@show');
        });
    });

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:posts']
    ], function () use ($router) {
        $router->post('/', 'PostsController@store');
        $router->put('/{id:[0-9]+}', 'PostsController@update');
        $router->delete('/{id:[0-9]+}', 'PostsController@destroy');

        // Locks
        $router->put('/{post_id:[0-9]+}/lock', 'LockController@store');
        $router->delete('/{post_id:[0-9]+}/lock', 'LockController@destroy');

        // Sub-post routes
        $router->group(['prefix' => '/{parent_id:[0-9]+}'], function () use ($router) {
            // Translations
            $router->group(['prefix' => 'translations'], function () use ($router) {
                $router->post('/', 'TranslationsController@store');
                $router->put('/{id:[0-9]+}', 'TranslationsController@update');
                $router->delete('/{id:[0-9]+}', 'TranslationsController@destroy');
                $router->put('/{locale:[A-Za-z_]+}', 'TranslationsController@update');
                $router->delete('/{locale:[A-Za-z_]+}', 'TranslationsController@destroy');
            });
        });
    });
});
