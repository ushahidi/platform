<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Groups Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_Forms_Groups extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'forms';
	}

	/**
	 * Create a new group
	 *
	 * POST /api/forms/:form_id/groups
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('form_groups', 'create');

		$request = ['form_id' => $this->request->param('form_id')] + $this->_request_payload;

		$this->_restful($endpoint, $request);
	}

	/**
	 * Retrieve all groups
	 *
	 * GET /api/forms/:form_id/groups
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('form_groups', 'search');

		$request = ['form_id' => $this->request->param('form_id')];

		$this->_restful($endpoint, $request);
	}

	/**
	 * Get the standard request parameters from the URL.
	 * @return Array [form_id, id]
	 */
	private function _get_request_params()
	{
		return [
			'form_id' => $this->request->param('form_id'),
			'id'      => $this->request->param('id'),
		];
	}

	/**
	 * Retrieve a group
	 *
	 * GET /api/forms/:form_id/groups/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('form_groups', 'read');

		$request = $this->_get_request_params();

		$this->_restful($endpoint, $request);
	}

	/**
	 * Update a single group
	 *
	 * PUT /api/forms/:form_id/groups/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('factory.endpoint')->get('form_groups', 'update');

		$request = $this->_request_payload + $this->_get_request_params();

		$this->_restful($endpoint, $request);
	}

	/**
	 * Delete a single group
	 *
	 * DELETE /api/forms/:form_id/groups/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$endpoint = service('factory.endpoint')->get('form_groups', 'delete');

		$request = $this->_get_request_params();

		$this->_restful($endpoint, $request);
	}
}
