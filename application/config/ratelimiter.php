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
	// memcache, filesystem or FALSE
	'cache' => 'memcache',
	'filesystem' => [
		'directory' => '/tmp',
	],
];