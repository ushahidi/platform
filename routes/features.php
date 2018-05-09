<?php

/*
|--------------------------------------------------------------------------
| Feature Routes
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

$router->group([
	'prefix' => $apiBase,
	'namespace' => 'API'
], function () use ($router) {

	// HXL
	$router->group([
		'prefix' => 'hxl',
		'middleware' => ['feature:hxl']
	], function () use ($router) {
		$router->get('/', "HXLController@index");
	});

	// User Settings
	$router->group([
		'namespace' => 'Users',
		'prefix' => 'users/{user_id:[0-9]+}',
		'middleware' => [
			'auth:api',
			'scope:users',
			'feature:user-settings'
		]
	], function () use ($router) {
		// Settings
		$router->group([
			'prefix' => 'settings',
			'middleware' => ['feature:user-settings']
		], function () use ($router) {
			$router->get('/', 'SettingsController@index');
			$router->get('/{id:[0-9]+}', 'SettingsController@show');
			$router->post('/', 'SettingsController@store');
			$router->put('/{id:[0-9]+}', 'SettingsController@update');
			$router->delete('/{id:[0-9]+}', 'SettingsController@destroy');
		});
	});
});
