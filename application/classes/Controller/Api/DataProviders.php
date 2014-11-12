<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API DataProvider Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_DataProviders extends Ushahidi_Rest {

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET    => 'get'
	);

	protected function _scope()
	{
		return 'dataproviders';
	}

	/**
	 * Retrieve All Enabled Data Providers
	 *
	 * GET /api/dataproviders
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('dataproviders', 'search');
		$this->_restful($endpoint, $this->request->query());
	}

	/**
	 * Retrieve A Provider
	 *
	 * GET /api/providers/:provider
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('dataproviders', 'read');
		$this->_restful($endpoint, $this->request->param());
	}
}
