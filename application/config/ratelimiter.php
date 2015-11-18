<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Ratelimiter Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return [
	/*  memcached, filesystem or FALSE
	 *
	 * When set to FALSE, in-memory cache will be used.
	 * Please note that this only lasts the lifetime of the request.
	 *
	 */
	'cache' => 'filesystem',
	'filesystem' => [
		'directory' => '/tmp/ratelimitercache',
	],
	'memcached' => [
		'host' => '127.0.0.1',
		'port' => 11211
	]
];
