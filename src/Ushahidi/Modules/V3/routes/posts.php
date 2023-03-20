<?php

/**
 *  @var $router \Illuminate\Routing\Router
 */

// Posts
$router->group([
    'namespace' => 'Posts',
    'prefix' => 'posts',
    'middleware' => ['scope:posts', 'expiration', 'invalidJSON'],
], function () use ($router) {
    // Public access
    $router->get('/', 'PostsController@index');
    // @todo stop using this in client, and remove?
    $router->options('/', ['uses' => 'PostsController@indexOptions']);
    $router->get('/{id}', 'PostsController@show')->where('id', '[0-9]+');

    // GeoJSON
    $router->get('/geojson', [
        'middleware' => add_cache_control('minimal'),
        'uses' => 'GeoJSONController@index',
    ]);
    $router->get('/geojson/{zoom}/{x}/{y}', 'GeoJSONController@index');
    $router->get('/{id}/geojson', 'GeoJSONController@show')->where('id', '[0-9]+');

    // Export
    // $router->get('/export', 'ExportController@index');

    // Stats
    $router->get('/stats', 'PostsController@stats');

    // Sub-post routes
    $router->group([
        'prefix' => '/{parent_id}',
        'where' => ['parent_id' => '[0-9]+'],
    ], function () use ($router) {
        // Revisions
        $router->group(['prefix' => 'revisions'], function () use ($router) {
            $router->get('/', 'RevisionsController@index');
            $router->get('/{id}', 'RevisionsController@show');
        });

        // Translations
        $router->group(['prefix' => 'translations'], function () use ($router) {
            $router->get('/', 'TranslationsController@index');
            $router->get('/{id}', 'TranslationsController@show')->where('id', '[0-9]+');
            $router->get('/{locale}', 'TranslationsController@show')->where('locale', '[A-Za-z_]+');
        });
    });

    // Restricted access
    $router->group([
        'middleware' => ['auth:api', 'scope:posts'],
    ], function () use ($router) {
        $router->post('/', 'PostsController@store');
        $router->put('/{id}', 'PostsController@update')->where('id', '[0-9]+');
        $router->delete('/{id}', 'PostsController@destroy')->where('id', '[0-9]+');

        // Locks
        $router->put('/{post_id}/lock', 'LockController@store')->where('post_id', '[0-9]+');
        $router->delete('/{post_id}/lock', 'LockController@destroy')->where('post_id', '[0-9]+');

        // Translations
        $router->group([
            'prefix' => '/{parent_id}/translations',
            'where' => ['parent_id' => '[0-9]+'],
        ], function () use ($router) {
            $router->post('/', 'TranslationsController@store');
            $router->put('/{id}', 'TranslationsController@update')->where('id', '[0-9]+');
            $router->delete('/{id}', 'TranslationsController@destroy')->where('id', '[0-9]+');
            $router->put('/{locale}', 'TranslationsController@update')->where('locale', '[A-Za-z_]+');
            $router->delete('/{locale}', 'TranslationsController@destroy')->where('locale', '[A-Za-z_]+');
        });
    });
});
