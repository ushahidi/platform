<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Forms extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'forms';
	}

	/**
	 * Create A Form
	 *
	 * POST /api/forms
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('forms', 'create');

		$this->_restful($endpoint, $this->_request_payload);
	}

	/**
	 * Retrieve All Forms
	 *
	 * GET /api/forms
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('forms', 'search');

		$this->_restful($endpoint, $this->request->query());
	}

	/**
	 * Retrieve A Form
	 *
	 * GET /api/forms/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('forms', 'read');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}

	/**
	 * Update A Form
	 *
	 * PUT /api/forms/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('factory.endpoint')->get('forms', 'update');

		$request = $this->_request_payload;
		$request['id'] = $this->request->param('id');

		$this->_restful($endpoint, $request);
	}

	/**
	 * Delete A Form
	 *
	 * DELETE /api/forms/:id
	 *
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$endpoint = service('factory.endpoint')->get('tags', 'delete');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}
}
