<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * HTTP 404 Exception
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Exception
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {

	/**
	 * @var  string  error rendering view
	 */
	public static $error_view = 'error/404';

	/**
	 * Generate a Response for the current Exception
	 *
	 * @uses   Kohana_Exception::response()
	 * @return Response
	 */
	public function get_response()
	{
		Kohana_Exception::$error_view = self::$error_view;

		return Kohana_Exception::response($this);
	}

}