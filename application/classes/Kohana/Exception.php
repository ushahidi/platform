<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Kohana Exception
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Exception
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Kohana_Exception extends Kohana_Kohana_Exception {
	/**
	 * @var  string  error rendering view
	 */
	public static $error_view = 'error/default';

	/**
	 * @var  string  error rendering view
	 */
	public static $error_layout = 'error/layout';

	/**
	 * Get a Response object representing the exception
	 *
	 * @uses    Kohana_Exception::text
	 * @param   Exception  $e
	 * @return  Response
	 */
	public static function response(Exception $e)
	{
		try
		{
			$response = parent::response($e);

			// Wrap default response in Ushahidi layout
			if (Kohana_Exception::$error_layout)
			{
				$view = View::factory(Kohana_Exception::$error_layout)
					->set('content', $response->body());
				$response->body($view->render());
			}
		}
		catch (Exception $e)
		{
			/**
			 * Things are going badly for us, Lets try to keep things under control by
			 * generating a simpler response object.
			 */
			$response = Response::factory();
			$response->status(500);
			$response->headers('Content-Type', 'text/plain');
			$response->body(Kohana_Exception::text($e));
		}

		return $response;
	}

}