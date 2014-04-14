<?php

/**
 * Ushahidi Init
 *
 * Clean code bootstrap
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// register this directory as an include path
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

// register a new autoloader for the Ushahidi namespace
spl_autoload_register(function($class) {
	$base_dir = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR;
	$ns = 'Ushahidi\\';
	$len = strlen($ns);

	// verify this is the expected namespace
	if (strncmp($ns, $class, $len) !== 0) {
		return;
	}
	
	// replace:
	// - namespace  -> base
	// - backslash  -> directory separator
	// append: .php
	$file = $base_dir . str_replace('\\', '/', $class) . '.php';

	if (file_exists($file)) {
		require $file;
	}
});

// create global depdendency container, using Pimple:
// http://pimple.sensiolabs.org/
function service($what = null) {
	static $container;
	if (!$container) {
		require 'Pimple/Pimple.php';

		$container = new \Pimple(array(
			'config.storage' => '\Ushahidi\Storage\Config',
			// 'user.storage' => '\Ushahidi\Storage\User\KohanaORM',
		));
	}
	if ($what) {
		return $container[$what];
	}
	return $container;
}

$services = service();
$services['config'] = function($c) {
	return new $c['config.storage']($c['config.backend']);
};

// $app['user'] = function($c) {
// 	return new $c['user.storage'];
// };
