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
		$response = parent::response($e);

		try
		{
			// Wrap default response in Ushahidi layout
			if (Kohana_Exception::$error_layout)
			{
				$view = View::factory(Kohana_Exception::$error_layout)
					->set('content', $response->body());
				$response->body($view->render());
			}
		}
		catch (Exception $new_e)
		{
			/**
			 * Things are going badly for us, just fall back to the default kohana response
			 */
		}

		return $response;
	}

	/**
	 * Override Exception handler to better handle exceptions at CLI
	 *
	 * @uses    Kohana_Exception::text
	 * @param   Exception   $e
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		if (PHP_SAPI == 'cli')
		{
			echo Kohana_Exception::text($e);

			$exit_code = $e->getCode();

			// Never exit "0" after an exception.
			if ($exit_code == 0)
			{
				$exit_code = 1;
			}

			exit($exit_code);
		}

		return parent::handler($e);
	}

}