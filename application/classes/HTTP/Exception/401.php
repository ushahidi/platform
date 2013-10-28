<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * HTTP 401 Exceptions
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Exception
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class HTTP_Exception_401 extends Kohana_HTTP_Exception_401 {
	/**
	 * Generate a Response for the current Exception
	 * 
	 * @uses   Kohana_Exception::response()
	 * @return Response
	 */
	public function get_response()
	{
		$this->check();

		return Kohana_Exception::response($this);
	}
	
}