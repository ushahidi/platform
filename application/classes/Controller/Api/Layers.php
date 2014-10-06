<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Layers Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Layers extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'layers';
	}

	/**
	 * Create A Layer
	 *
	 * POST /api/layers
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('layers', 'create');

		$this->_restful($endpoint, $this->_request_payload);
	}

	/**
	 * Retrieve All Layers
	 *
	 * GET /api/layers
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('layers', 'search');
		$request = $this->request->query();

		$this->_restful($endpoint, $request);
	}

	/**
	 * Retrieve A Layer
	 *
	 * GET /api/layers/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('layers', 'read');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}

	/**
	 * Update A Layer
	 *
	 * PUT /api/layers/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('factory.endpoint')->get('layers', 'update');

		$request = $this->_request_payload;
		$request['id'] = $this->request->param('id');

		$this->_restful($endpoint, $request);
	}

	/**
	 * Delete A Layer
	 *
	 * DELETE /api/layers/:id
	 *
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$endpoint = service('factory.endpoint')->get('layers', 'delete');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}
}
