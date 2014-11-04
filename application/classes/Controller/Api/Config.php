<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Config Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Config extends Ushahidi_Rest {

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET     => 'get',
		Http_Request::PUT     => 'put', // Typically Update..
		Http_Request::OPTIONS => 'options',
	);

	protected function _scope()
	{
		return 'config';
	}

	protected function _is_auth_required()
	{
		if (parent::_is_auth_required())
		{
			// Completely anonymous access is allowed for (some) GET requests.
			// Further checks are made down the stack.
			return ($this->request->method() !== Request::GET);
		}
		return FALSE;
	}

	/**
	 * Retrieve All Configs
	 *
	 * GET /api/config
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('config', 'search');

		$this->_restful($endpoint, $this->request->query());
	}

	/**
	 * Retrieve A Config
	 *
	 * GET /api/config/:group
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('config', 'read');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}

	/**
	 * Update A Config
	 *
	 * PUT /api/config/:group
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('factory.endpoint')->get('config', 'update');

		$request = $this->_request_payload;
		$request['id'] = $this->request->param('id');

		$this->_restful($endpoint, $request);
	}
}
