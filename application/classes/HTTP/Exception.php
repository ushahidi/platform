<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * HTTP Exception
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Exception
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class HTTP_Exception extends Kohana_HTTP_Exception {
	use Ushahidi_Corsheaders;

	/**
	 * Generate a Response for the current Exception
	 *
	 * @uses   Kohana_Exception::response()
	 * @return Response
	 */
	public function get_response()
	{
		$response = parent::get_response();
		$this->add_cors_headers($response);

		return $response;
	}

}
