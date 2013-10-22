<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Form Groups API SubRoute
 */
Route::set('form-groups', 'api/v2/forms/<form_id>/groups/<group_id>/<controller>(/<id>)',
	array(
		'form_id' => '\d+',
		'group_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Forms/Groups'
	));

/**
 * Forms API SubRoute
 */
Route::set('forms', 'api/v2/forms/<form_id>/<controller>(/<id>)',
	array(
		'form_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Forms'
	));

/**
 * GeoJSON API SubRoute
 */
Route::set('geojson', 'api/v2/posts/geojson(/<zoom>/<x>/<y>)',
	array(
		'zoom' => '\d+',
		'x' => '\d+',
		'y' => '\d+',
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'GeoJSON',
		'directory'  => 'Api/Posts'
	));

/**
 * GeoJSON API SubRoute
 */
Route::set('geojson-post-id', 'api/v2/posts/<id>/geojson',
	array(
		'id' => '\d+',
		'zoom' => '\d+',
		'x' => '\d+',
		'y' => '\d+',
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'GeoJSON',
		'directory'  => 'Api/Posts'
	));

/**
 * Posts API SubRoute
 */
Route::set('posts', 'api/v2/posts/<post_id>/<controller>(/<id>)',
	array(
		'post_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Posts'
	));

/**
 * Base Ushahidi API Route
 */
Route::set('api', 'api/v2(/<controller>(/<id>))',
	array(
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api'
	));

/**
 * Translations API SubRoute
 */
Route::set('translations', 'api/v2/posts/<post_id>/translations(/<locale>)',
	array(
		'post_id' => '\d+',
		'locale' => '[a-zA-Z_]+'
	))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'Translations',
		'directory'  => 'Api/Posts'
	));

/**
 * Default Route
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'ushahidi',
		'action'     => 'index',
	));