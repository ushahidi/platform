<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Attributes Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_API_Forms_Attributes extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'forms';
	}

	/**
	 * Create a new attribute
	 *
	 * POST /api/forms/:form_id/attributes
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('form_attributes', 'create');
		$this->_restful($endpoint, $this->_request_payload + $this->request->param());
	}

	/**
	 * Update an attribute
	 *
	 * PUT /api/forms/:form_id/attributes/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('factory.endpoint')->get('form_attributes', 'update');
		$this->_restful($endpoint, $this->_request_payload + $this->request->param());
	}

	/**
	 * Retrieve form's attributes
	 *
	 * GET /api/forms/:form_id/attributes
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('form_attributes', 'search');
		$this->_restful($endpoint, $this->request->param() + $this->request->query());
	}

	/**
	 * Retrieve an attribute
	 *
	 * GET /api/forms/:form_id/attributes/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('form_attributes', 'read');
		$this->_restful($endpoint, $this->request->param() + $this->request->query());
	}

	/**
	 * Delete an attribute
	 *
	 * DELETE /api/forms/:form_id/attributes/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$endpoint = service('factory.endpoint')->get('form_attributes', 'delete');
		$this->_restful($endpoint, $this->request->param());
	}
}
