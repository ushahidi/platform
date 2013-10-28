<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Auth Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array(

	'driver'       => 'ORM',
	'hash_method'  => 'sha256',
	'hash_key'     => 'somereallylongkey',
	'lifetime'     => 1209600,
	'session_type' => Session::$default,
	'session_key'  => 'auth_user',

	// Username/password combinations for the Auth File driver
	'users' => array(
		// 'admin' => 'b3154acf3a344170077d11bdb5fff31532f679a1919e716a02',
		'admin' => ''
	),

);
