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
		$this->_restful($endpoint, $this->_request_payload + $this->request->param());
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
		$this->_restful($endpoint, $this->request->param());
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
		$this->_restful($endpoint, $this->request->param());
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
		$this->_restful($endpoint, $this->_request_payload + $this->request->param());
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
		$this->_restful($endpoint, $this->request->param());
	}
}
