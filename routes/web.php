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
$apiBase = 'api/v' . $apiVersion . '/';

$app->get('{anything:.*}', function ($path) use ($app) {
	return \Request::factory($path, array(), false)
		->execute()
		->send_headers(true)
		->body();
});
$app->post('{anything:.*}', function ($path) use ($app) {
	return \Request::factory($path, array(), false)
		->execute()
		->send_headers(true)
		->body();
});
$app->put('{anything:.*}', function ($path) use ($app) {
	return \Request::factory($path, array(), false)
		->execute()
		->send_headers(true)
		->body();
});
$app->delete('{anything:.*}', function ($path) use ($app) {
	return \Request::factory($path, array(), false)
		->execute()
		->send_headers(true)
		->body();
});
