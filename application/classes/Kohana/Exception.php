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
	public static $error_view = 'error/api';

	/**
	 * @var  string  error view content type
	 */
	public static $error_view_content_type = 'application/json';

}
