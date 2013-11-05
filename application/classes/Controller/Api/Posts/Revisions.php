<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Revisions Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Posts_Revisions extends Controller_Api_Posts {

	protected $_type = 'revision';

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET    => 'get',
	);
}