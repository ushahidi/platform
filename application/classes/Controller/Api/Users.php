<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Users Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Users extends Ushahidi_Api {

	/**
	 * @var string Field to sort results by
	 */
	 protected $record_orderby = 'created';

	/**
	 * @var string Direct to sort results
	 */
	 protected $record_order = 'DESC';

	/**
	 * @var int Maximum number of results to return
	 */
	 protected $record_allowed_orderby = array('id', 'created', 'email', 'username');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'users';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'users';

		$this->_resource = ORM::factory('User');

		// Get post
		if ($user_id = $this->request->param('id', 0))
		{
			if ($user_id == 'me')
			{
				$user = $this->user;

				if ( ! $user->loaded())
				{
					throw new HTTP_Exception_404('No user associated with the access token.');
				}

				$this->_resource = $user;
			}
			else
			{
			$user = ORM::factory('User', $user_id);

			if (! $user->loaded())
			{
				throw new HTTP_Exception_404('User does not exist. ID: \':id\'', array(
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $user;
		}
	}
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
		$post = $this->_request_payload;

		$user = $this->resource();

		$this->create_or_update_user($user, $post);
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
		$results = array();

		$this->_prepare_order_limit_params();

		$users_query = ORM::factory('User')
				->order_by($this->_record_orderby, $this->_record_order)
				->offset($this->_record_offset)
				->limit($this->_record_limit);

		//Prepare search params
		$q = $this->request->query('q');
		if (! empty($q))
		{
			$users_query->and_where_open();
			$users_query->where('email', 'LIKE', "%$q%");
			$users_query->or_where('username', 'LIKE', "%$q%");
			$users_query->or_where('first_name', 'LIKE', "%$q%");
			$users_query->or_where('last_name', 'LIKE', "%$q%");
			$users_query->and_where_close();
		}

		$user = $this->request->query('email');
		if (! empty($user))
		{
			$users_query->where('email', '=', $user);
		}

		$first_name = $this->request->query('first_name');
		if (! empty($first_name))
		{
			$users_query->where('first_name', '=', $first_name);
		}

		$last_name = $this->request->query('last_name');
		if (! empty($last_name))
		{
			$users_query->where('last_name', '=', $last_name);
		}

		$username = $this->request->query('username');
		if (! empty($username))
		{
			$users_query->where('username', '=', $username);
		}

		// Get the count of ALL records
		$count_query = clone $users_query;
		$total_records = (int) $count_query
			->select(array(DB::expr('COUNT(DISTINCT `user`.`id`)'), 'records_found'))
			->limit(NULL)
			->offset(NULL)
			->find_all()
			->get('records_found');

		// Get posts
		$users = $users_query->find_all();

		foreach ($users as $user)
		{
			// Check if user is allowed to access this user
			if ($this->acl->is_allowed($this->user, $user, 'get') )
			{
				$result = $user->for_api();
				$result['allowed_methods'] = $this->_allowed_methods($user);
				$results[] = $result;
			}
		}

		// @todo should we update this to only count users we actually allowed to see?
		$count = $users->count();

		// Current/Next/Prev urls
		$params = array(
				'limit' => $this->_record_limit,
				'offset' => $this->_record_offset,
		);

		// Only add order/orderby if they're already set
		if ($this->request->query('orderby') OR $this->request->query('order'))
		{
			$params['orderby'] = $this->_record_orderby;
			$params['order'] = $this->_record_order;
		}

		$prev_params = $next_params = $params;
		$next_params['offset'] = $params['offset'] + $params['limit'];
		$prev_params['offset'] = $params['offset'] - $params['limit'];
		$prev_params['offset'] = $prev_params['offset'] > 0 ? $prev_params['offset'] : 0;

		$curr = URL::site($this->request->uri() . URL::query($params), $this->request);
		$next = URL::site($this->request->uri() . URL::query($next_params), $this->request);
		$prev = URL::site($this->request->uri() . URL::query($prev_params), $this->request);

		//Respond with Users
		$this->_response_payload = array(
				'count' => $count,
				'total_count' => $total_records,
				'results' => $results,
				'limit' => $this->_record_limit,
				'offset' => $this->_record_offset,
				'order' => $this->_record_order,
				'orderby' => $this->_record_orderby,
				'curr' => $curr,
				'next' => $next,
				'prev' => $prev,
		);

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
		$user = $this->resource();

		$this->_response_payload = $user->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();

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
		$post = $this->_request_payload;

		$user = $this->resource();

		$this->create_or_update_user($user, $post);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Delete A User
	 *
	 * DELETE /api/user/:id
	 *
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$user = $this->resource();
		$this->_response_payload = array();
		if ( $user->loaded() )
		{
			// Return the user we just deleted (provides some confirmation)
			$this->_response_payload = $user->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
			$user->delete();
		}
		}


	/**
	 * Save users
	 *
	 * @param User_Model $user
	 * @aparam array $post POST data
	 */
	 protected function create_or_update_user($user, $post)
	 {
	 	// If the password is empty, delete the value
	 	// This ensures we don't overwrite a password with null.
	 	if (empty($post['password']))
	 	{
	 		unset($post['password']);
	 	}

		$user->values($post, array('username', 'password', 'first_name', 'last_name', 'email'));

		//Validation - cycle through nested models and perform in-model
		//validation before saving

		try
		{
			// Validate base user data
			$user_validation = Validation::factory($post);
			$user_validation->rule('username', 'not_empty');
			// If this is a new user, require password
			if (! $user->loaded()) $user_validation->rule('password', 'not_empty');
			$user->check($user_validation);

			// Validates ... so save
			$user->save();

			// Response is the user
			$this->_response_payload = $user->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($user);
		}
		catch(ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
					':errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
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
