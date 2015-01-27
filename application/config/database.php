<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Database Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array
(
	'default' => array
	(
		'type'       => getenv('DB_TYPE'),
		'connection' => array(
			'hostname'   => getenv('DB_HOST'),
			'database'   => getenv('DB_NAME'),
			'username'   => getenv('DB_USER'),
			'password'   => getenv('DB_PASS'),
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => TRUE,
		'profiling'    => TRUE,
	)
);
