<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Sets Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Sets extends Ushahidi_Api {

	/**
	 * @var string Field to sort results by
	 */
	protected $_record_orderby = 'created';

	/**
	 * @var string Direct to sort results
	 */
	protected $_record_order = 'DESC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id', 'created', 'name');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'sets';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'sets';

		$this->_resource = ORM::factory('Set');

		// Get post
		if ($set_id = $this->request->param('id', 0))
		{
			// Respond with set
			$set = ORM::factory('Set', $set_id);

			if (! $set->loaded())
			{
				throw new HTTP_Exception_404('Set does not exist. ID: \':id\'', array(
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $set;
		}
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
		$post = $this->_request_payload;

		$set = $this->resource();

		$this->create_or_update_set($set, $post);
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
		$results = array();

		$this->_prepare_order_limit_params();

		$sets_query = ORM::factory('Set')
				->order_by($this->_record_orderby, $this->_record_order)
				->offset($this->_record_offset)
				->limit($this->_record_limit);

		//Prepare search params
		$q = $this->request->query('q');
		if (! empty($q))
		{
			$sets_query->where('name', 'LIKE', "%$q%");
		}

		$set = $this->request->query('name');
		if (! empty($set))
		{
			$sets_query->where('name', '=', $set);
		}

		$user = $this->request->query('user');
		if(! empty($user))
		{
			$sets_query->where('user_id', '=', $user);
		}

		$sets = $sets_query->find_all();

		$count = $sets->count();

		foreach ($sets as $set)
		{
			// Check if user is allowed to access this set
			if ($this->acl->is_allowed($this->user, $set, 'get') )
			{
				$result = $set->for_api();
				$result['allowed_methods'] = $this->_allowed_methods($set);
				$results[] = $result;
			}
		}

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

		//Respond with Sets
		$this->_response_payload = array(
				'count' => $count,
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
	 * Retrieve A Set
	 *
	 * GET /api/sets/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$set = $this->resource();

		$this->_response_payload = $set->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
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
		$post = $this->_request_payload;

		$set = $this->resource();

		$this->create_or_update_set($set, $post);

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
		$set = $this->resource();
		$this->_response_payload = array();
		if ( $set->loaded() )
		{
			// Return the set we just deleted (provides some confirmation)
			$this->_response_payload = $set->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
			$set->delete();
		}
	}


	/**
	 * Save sets
	 *
	 * @param Set_Model $set
	 * @aparam array $post POST data
	 */
	 protected function create_or_update_set($set, $post)
	 {
		$set->values($post, array(
				'name', 'filter', 'user_id'));

		//Validation - cycle through nested models and perform in-model
		//validation before saving

		try
		{
			// Validate base set data
			$set->check();

			$set->save();

			// Response is the set
			$this->_response_payload = $set->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($set);
		}
		catch(ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
					':errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}


	 }
}
