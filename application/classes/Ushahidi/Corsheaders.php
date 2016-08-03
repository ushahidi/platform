<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi CORSHeaders Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

trait Ushahidi_Corsheaders {

	protected function add_cors_headers(HTTP_Response &$response)
	{
		$response->headers('Access-Control-Allow-Origin', '*');
		$response->headers('Access-Control-Allow-Headers', 'Authorization, Content-type');

		if (isset($this->_action_map))
		{
			$allow_methods = implode(', ', array_keys($this->_action_map));
			$response->headers('Allow', $allow_methods);
			$response->headers('Access-Control-Allow-Methods', $allow_methods);
		}

		return $response;
	}

	protected static function static_add_cors_headers(HTTP_Response &$response)
	{
		$response->headers('Access-Control-Allow-Origin', '*');
		$response->headers('Access-Control-Allow-Headers', 'Authorization, Content-type');

		return $response;
	}

}
