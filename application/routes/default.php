<?php defined('SYSPATH') OR die('No direct script access.');


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
 * Forms API SubRoute
 */	
Route::set('forms', 'api/v2/forms/<form_id>(/<controller>(/<id>))', 
	array(
		'form_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Forms'
	));

/**
 * Forms API SubRoute
 */	
Route::set('forms', 'api/v2/forms/<form_id>(/<controller>(/<id>(/<action>)))', 
	array(
		'form_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
		'directory'  => 'Api/Forms'
	));

/**
 * Posts API SubRoute
 */	
Route::set('posts', 'api/v2/posts/<post_id>(/<controller>(/<id>))', 
	array(
		'post_id' => '\d+',
		'id' => '\d+'
	))
	->defaults(array(
		'action'     => 'index',
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
