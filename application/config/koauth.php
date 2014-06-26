<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'default_scope'    => NULL,
	'supported_scopes' => array(
		'api',
		'posts',
		'forms',
		'sets',
		'tags',
		'users',
		'media',
		'config',
		'messages',
		'dataproviders',
		'stats'
	),
	'www_realm'        => 'Ushahidi API',
	'access_lifetime'  => 86400 // 1 day
);
