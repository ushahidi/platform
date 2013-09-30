<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Ushahidi Frontend Routes
 */
Route::set('index', '(/<action>)')
	->defaults(array(
		'controller' => 'Main',
		'action'     => 'index',
	));

