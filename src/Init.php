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
			'config.storage' => '\Ushahidi\Storage\Kohana\ConfigRepository',
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
		$conf = $config->get('features');
	} catch (\Exception $e) {
		return false;
	}
	return !empty($conf->$name);
}


$services = service();

// Standard services
$services['config'] = function($c) {
	return new $c['config.storage']($c['config.backend']);
};

// $app['user'] = function($c) {
// 	return new $c['user.storage'];
// };
