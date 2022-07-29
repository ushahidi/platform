<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * API version number
 */
$apiVersion = '3';
$apiBase = '/v'.$apiVersion;

Route::get($apiBase, "API\IndexController@index");
Route::group([
    'prefix' => $apiBase,
    'namespace' => 'API',
], function (Router $router) {
    require __DIR__. '/v3/index.php';
});
