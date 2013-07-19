<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Register Koauth autoloader
 */
spl_autoload_register(array('Koauth', 'auto_load'));

/**
 * OAuth Route
 * Have to add this manually because the class is OAuth not Oauth
 */
Route::set('oauth', 'oauth(/<action>)')
	->defaults(array(
		'controller' => 'OAuth',
		'action'     => 'index',
	));

