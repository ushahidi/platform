<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi API CSV Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_CSV extends Controller_Api_Media
{
	protected function _scope()
	{
		return 'csv';
	}
}
