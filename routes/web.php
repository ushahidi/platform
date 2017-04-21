<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return \Request::factory('/', array(), false)
        ->execute()
        ->send_headers(true)
        ->body();
});

/**
 * API version number
 */
$apiVersion = '3';
$apiBase = 'api/v' . $apiVersion;

$app->group(['prefix' => $apiBase, 'namespace' => 'API'], function () use ($app) {

    // Define /config outside the group otherwise prefix breaks optional trailing slash
    $app->get('/config[/]', ['uses' => 'ConfigController@index']);
    // $app->post('/config[/]', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@store']);
    $app->group(['prefix' => 'config/'], function () use ($app) {
        $app->get('/{id}[/]', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@show']);
        $app->put('/{id}[/]', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@update']);
        // $app->delete('/{id}[/]', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@destroy']);
    });

    $app->group(['middleware' => ['auth:api', 'scope:tags']], function () use ($app) {
        $app->get('/tags[/]', 'TagsController@index');
        $app->post('/tags[/]', 'TagsController@store');
        $app->group(['prefix' => 'tags/'], function () use ($app) {
            $app->get('/{id}[/]', 'TagsController@show');
            $app->put('/{id}[/]', 'TagsController@update');
            $app->delete('/{id}[/]', 'TagsController@destroy');
        });
    });
});

// $app->get('{anything:.*}', function ($path) use ($app) {
//     return \Request::factory($path, array(), false)
//         ->execute()
//         ->send_headers(true)
//         ->body();
// });
// $app->post('{anything:.*}', function ($path) use ($app) {
//     return \Request::factory($path, array(), false)
//         ->execute()
//         ->send_headers(true)
//         ->body();
// });
// $app->put('{anything:.*}', function ($path) use ($app) {
//     return \Request::factory($path, array(), false)
//         ->execute()
//         ->send_headers(true)
//         ->body();
// });
// $app->delete('{anything:.*}', function ($path) use ($app) {
//     return \Request::factory($path, array(), false)
//         ->execute()
//         ->send_headers(true)
//         ->body();
// });
