<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Ushahidi Frontend Routes
 */
Route::set('index', '(<misc>)',
	array(
		'misc' => '^(?!api|oauth|user|media).*',
	))
	->defaults(array(
		'controller' => 'Main',
		'action'     => 'index',
	));

