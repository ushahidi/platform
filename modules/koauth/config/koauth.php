<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'default_scope'    => NULL, 
	'supported_scopes' => array(
		'api',
		'profile',
	),
	'www_realm'        => 'Kohana API',
	'access_lifetime'  => 3600
);
