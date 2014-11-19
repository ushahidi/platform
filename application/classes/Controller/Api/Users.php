<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Users Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Users extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'users';
	}

	/**
	 * Create A User
	 *
	 * POST /api/users
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('users', 'create');

		$this->_restful($endpoint, $this->_request_payload);
	}


	/**
	 * Retrieve All Users
	 *
	 * GET /api/users
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('factory.endpoint')->get('users', 'search');

		$this->_restful($endpoint, $this->request->query());
	}

	/**
	 * Retrieve A User
	 *
	 * GET /api/users/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('factory.endpoint')->get('users', 'read');

		$id = $this->request->param('id');
		if ($id === 'me' and service('session.user')->id)
		{
		    $id = service('session.user')->id;
		}

		$request = compact('id');

		$this->_restful($endpoint, $request);
	}

	/**
	 * Update A User
	 *
	 * PUT /api/users/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('factory.endpoint')->get('users', 'update');

		$id = $this->request->param('id');
		if ($id === 'me' and service('session.user')->id)
		{
		    $id = service('session.user')->id;
		}

		$request       = $this->_request_payload;
		$request['id'] = $id;

		$this->_restful($endpoint, $request);
	}

	/**
	 * Delete A User
	 *
	 * DELETE /api/user/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$endpoint = service('factory.endpoint')->get('users', 'delete');
		$request = ['id' => $this->request->param('id')];
		$this->_restful($endpoint, $request);
	}

	/**
	 * Get current user
	 *
	 * GET /api/users/me
	 *
	 * @return void
	 */
	public function action_get_me()
	{
		$this->action_get_index();
	}

	/**
	 * Update current user
	 *
	 * PUT /api/users/me
	 *
	 * @return void
	 */
	public function action_put_me()
	{
		$this->action_put_index();
	}
}
