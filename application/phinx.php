<?php

require_once __DIR__ . '/kohana.php';

$db = service('db.config');

return [
	'paths' => [
		'migrations' => realpath(APPPATH . '../migrations'),
	],
	'environments' => [
		'default_migration_table' => 'phinxlog',
		'default_database' => 'ushahidi',
		'ushahidi' => [
			'adapter' => 'mysql', // todo: how to make this dynamic?
			'host' => $db['connection']['hostname'],
			'name' => $db['connection']['database'],
			'user' => $db['connection']['username'],
			'pass' => $db['connection']['password'],
			'charset' => $db['charset'],
		],
	]
];
