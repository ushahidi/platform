<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Sets Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Sets extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'sets';
	}

	/**
	 * Create A Set
	 *
	 * POST /api/sets
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('sets', 'create');

		$this->_restful($endpoint, $this->_request_payload);
	}

	/**
	 * Retrieve All Sets
	 *
	 * GET /api/sets
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('sets', 'search');

		$this->_restful($endpoint, $this->request->query());

	}

	/**
	 * Retrieve A Set
	 *
	 * GET /api/sets/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('sets', 'read');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
		
	}


	/**
	 * Update A Set
	 *
	 * PUT /api/sets/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('factory.endpoint')->get('sets', 'update');

		$request = $this->_request_payload;
		$request['id'] = $this->request->param('id');

		$this->_restful($endpoint, $request);
	}

	/**
	 * Delete A Set
	 *
	 * DELETE /api/sets/:id
	 *
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$endpoint = service('factory.endpoint')->get('sets', 'delete');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}
}
