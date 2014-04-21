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

require __DIR__ . '/../vendor/autoload.php';

// global depdendency container, using Pimple:
// http://pimple.sensiolabs.org/
function service($what = null) {
	static $container;
	if (!$container) {
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

// global feature checking
function feature($name) {
	$config = service('config');
	try {
		$conf = $config->get('features', $name);
	} catch (\Exception $e) {
		return false;
	}
	return (bool) $conf->value;
}


$services = service();

// Standard services
$services['config'] = function($c) {
	return new $c['config.storage']($c['config.backend']);
};

// $app['user'] = function($c) {
// 	return new $c['user.storage'];
// };

// Formatters and other helpers
$services['config.format.hash'] = $services->protect(function(array $configGroup) {
	$hash = array();
	foreach ($configGroup as $config) {
		$hash[$config->key] = $config->value;
	}
	return $hash;
});
