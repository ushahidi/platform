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

/**
 * API version number
 */
$apiVersion = '3';
$apiBase = 'api/v' . $apiVersion;

$router->get('/', "API\IndexController@index");
$router->get($apiBase, "API\IndexController@index");
$router->group([
    'prefix' => $apiBase,
    'namespace' => 'API'
], function () use ($router) {

    require __DIR__.'/auth.php';

    require __DIR__.'/apikeys.php';
    require __DIR__.'/collections.php';
    require __DIR__.'/config.php';
    require __DIR__.'/contacts.php';
    require __DIR__.'/csv.php';
    require __DIR__.'/country-codes.php';
    require __DIR__.'/dataproviders.php';
    require __DIR__.'/exports.php';
    require __DIR__.'/forms.php';
    require __DIR__.'/hxl.php';
    require __DIR__.'/layers.php';
    require __DIR__.'/media.php';
    require __DIR__.'/messages.php';
    require __DIR__.'/migration.php';
    require __DIR__.'/notifications.php';
    require __DIR__.'/permissions.php';
    require __DIR__.'/posts.php';
    require __DIR__.'/roles.php';
    require __DIR__.'/savedsearches.php';
    require __DIR__.'/tags.php';
    require __DIR__.'/tos.php';
    require __DIR__.'/users.php';
    require __DIR__.'/webhooks.php';
});

// Migration
$router->get('/migrate', 'MigrateController@migrate');
