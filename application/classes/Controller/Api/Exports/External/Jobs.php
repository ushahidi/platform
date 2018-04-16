<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API External Webhook Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Exports_External_Jobs extends Controller_Api_External {

    protected function _scope()
	{
		return 'export_jobs';
	}
}
