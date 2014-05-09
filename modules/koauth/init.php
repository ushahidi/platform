<?php defined('SYSPATH') or die('No direct script access.');

/**
 * OAuth Route
 * Have to add this manually because the class is OAuth not Oauth
 */
Route::set('oauth', 'oauth(/<action>)')
	->defaults(array(
		'controller' => 'OAuth',
		'action'     => 'index',
	));

