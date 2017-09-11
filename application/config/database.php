<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Database Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// Parse ClearDB URLs
if (getenv("CLEARDB_DATABASE_URL")) {
	$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
	// Push url parts into env
	putenv("DB_HOST=" . $url["host"]);
	putenv("DB_USER=" . $url["user"]);
	putenv("DB_PASS=" . $url["pass"]);
	putenv("DB_NAME=" . substr($url["path"], 1));
	// Assuming ClearDB is always MySQLi
	// @todo parse $url['scheme'] instead
	putenv("DB_TYPE=" . "MySQLi");
}

// Default to MySQLi db if not set
// This at least results in a connect error if other vars aren't set
if (! getenv('DB_TYPE')) {
	putenv("DB_TYPE=MySQLi");
}

// DB config
$config = [
	'type'       => getenv('DB_TYPE'),
	'connection' => [
		'hostname'   => getenv('DB_HOST'),
		'database'   => getenv('DB_NAME'),
		'username'   => getenv('DB_USER'),
		'password'   => getenv('DB_PASS'),
		'persistent' => FALSE,
	],
	'table_prefix' => '',
	'charset'      => 'utf8',
	'caching'      => TRUE,
	'profiling'    => TRUE,
];

// If multisite is enabled
if (!empty(getenv("MULTISITE_DOMAIN"))) {
	// Use this config for the multisite db
	return [
		// Just define basics for default connection
		'default'   => [
			'type'         => getenv('DB_TYPE'),
			'connection'   => [ 'persistent' => FALSE, ],
			'table_prefix' => '',
			'charset'      => 'utf8',
			'caching'      => TRUE,
			'profiling'    => TRUE,
		],
		'multisite' => $config
	];
} else {
	// Otherwise this is the platform DB config
	return [
		'default' => $config
	];
}
