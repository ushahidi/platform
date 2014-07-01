<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Messages Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Messages extends Ushahidi_Api {

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
	protected $_record_allowed_orderby = array('id', 'created');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'messages';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'messages';

		$this->_resource = ORM::factory('Message');

		// Get post
		if ($message_id = $this->request->param('id', 0))
		{
			$message = ORM::factory('Message', $message_id);

			if (! $message->loaded())
			{
				throw new HTTP_Exception_404('Message does not exist. ID: \':id\'', array(
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $message;
		}
	}

	/**
	 * Create A Message
	 *
	 * POST /api/messages
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post = $this->_request_payload;

		$message = $this->resource();

		$this->create_or_update_message($message, $post);
	}

	/**
	 * Retrieve All Messages
	 *
	 * GET /api/messages
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$this->_prepare_order_limit_params();

		$messages_query = ORM::factory('Message')
			->join('contacts')
			->on('contact_id', '=', 'contacts.id')
			->order_by('message.' . $this->_record_orderby, $this->_record_order)
			->offset($this->_record_offset)
			->limit($this->_record_limit);

		// Get the requested box, default is "all"
		$box = $this->request->query('box');

		if ($box === 'outbox')
		{
			// Outbox only shows outgoing messages
			$messages_query->where('direction', '=', 'outgoing');
		}
		elseif ($box === 'inbox')
		{
			// Inbox only shows incoming messages
			$messages_query->where('direction', '=', 'incoming');
		}

		// Get the requested status, which is secondary to box
		$status = $this->request->query('status');

		if ($box === 'archived')
		{
			// Archive only shows archived messages
			$messages_query->where('status', '=', 'archived');
		}
		elseif ($status)
		{
			if ($status !== 'all') {
				// Search for a specific status
				$messages_query->where('status', '=', $status);
			}
			// "all" status does nothing :)
		}
		else
		{
			// Other boxes do not display archived
			$messages_query->where('status', '!=', 'archived');
		}

		// Prepare search params
		// @todo generalize this?
		$q = $this->request->query('q');
		if (! empty($q))
		{
			$messages_query->and_where_open();
			$messages_query->where('contacts.contact', 'LIKE', "%$q%");
			$messages_query->or_where('title', 'LIKE', "%$q%");
			$messages_query->or_where('message', 'LIKE', "%$q%");
			$messages_query->and_where_close();
		}

		$type = $this->request->query('type');
		if (! empty($type))
		{
			$messages_query->where('message.type', '=', $type);
		}

		$type = $this->request->query('parent');
		if (! empty($type))
		{
			$messages_query->where('parent_id', '=', $type);
		}

		$contact = $this->request->query('contact');
		if (! empty($contact))
		{
			$messages_query->where('contact_id', '=', $contact);
		}

		$data_provider = $this->request->query('data_provider');
		if (! empty($data_provider))
		{
			$messages_query->where('message.data_provider', '=', $data_provider);
		}

		$post = $this->request->query('post');
		if (! empty($post))
		{
			$messages_query->where('post_id', '=', $post);
		}

		// Get the count of ALL records
		$count_query = clone $messages_query;
		$total_records = (int) $count_query
			->select(array(DB::expr('COUNT(DISTINCT `message`.`id`)'), 'records_found'))
			->limit(NULL)
			->offset(NULL)
			->find_all()
			->get('records_found');
		$count_query_sql = $count_query->last_query();

		// Get posts
		$messages = $messages_query->find_all();
		$messages_query_sql = $messages_query->last_query();

		//$count = $messages->count();

		foreach ($messages as $message)
		{
			// Check if user is allowed to access this message
			if ($this->acl->is_allowed($this->user, $message, 'get') )
			{
				$result = $message->for_api();
				$result['allowed_methods'] = $this->_allowed_methods($message);
				$results[] = $result;
			}
		}

		// Count actual results since they're filtered by access check
		$count = count($results);

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

		// Respond with posts
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
	 * Retrieve A Message
	 *
	 * GET /api/messages/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$message = $this->resource();

		$this->_response_payload = $message->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Update A Message
	 *
	 * PUT /api/messages/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$post = $this->_request_payload;

		$message = $this->resource();

		$this->create_or_update_message($message, $post);
	}

	/**
	 * Delete A Message
	 *
	 * DELETE /api/messages/:id
	 *
	 * @return void
	 * @todo Authentication
	 */
	/*public function action_delete_index()
	{
		$message = $this->resource();
		$this->_response_payload = array();
		if ( $message->loaded() )
		{
			// Return the form we just deleted (provides some confirmation)
			$this->_response_payload = $message->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
			$message->delete();
		}
	}*/

	/**
	 * Save messages
	 *
	 * @param Message_Model $message
	 * @param array $post POST data
	 */
	protected function create_or_update_message($message, $post)
	{
		// unpack user to get contact_id
		if (isset($post['contact']))
		{
			if (is_array($post['contact']) AND isset($post['contact']['id']))
			{
				$post['contact_id'] = $post['contact']['id'];
			}
			elseif (! is_array($post['contact']))
			{
				$post['contact_id'] = $post['contact'];
			}
		}

		// unpack user to get contact_id
		if (isset($post['parent']))
		{
			if (is_array($post['parent']) AND isset($post['parent']['id']))
			{
				$post['parent_id'] = $post['parent']['id'];
			}
			elseif (! is_array($post['parent']))
			{
				$post['parent_id'] = $post['parent'];
			}
		}

		// Validation & saving
		try
		{
			$validation = Validation::factory($post);

			// If message is new
			if (! $message->loaded())
			{
				$message->values($post, array('parent_id', 'contact_id', 'data_provider', 'title', 'message', 'datetime', 'type', 'direction'));
				$message->status = 'pending';

				// Users can only create outgoing messages.
				$validation->rule('direction', 'equals', array(':value', 'outgoing'));
			}
			// Else: must be an existing message
			else
			{
				// incoming
				if ($message->direction == 'incoming')
				{
					// Really limited update, users can't actually edit a message, just archived/unarchive
					$message->values($post, array('status'));
				}
				// outgoing
				else
				{
					// Update most values, exclude direction and parent id.
					$message->values($post, array('contact_id', 'data_provider', 'title', 'message', 'datetime', 'type', 'status'));

					// Shouldn't be setting to failed, unknown or sent. Only Pending, expired and cancelled should be set by the user.
					$validation->rule('status', 'in_array', array(':value', array('pending', 'expired', 'cancelled')));
				}
			}

			$message->check($validation);

			$message->save();

			// Response is the complete form
			$this->_response_payload = $message->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}

	/**
	 * Create post from message
	 *
	 * POST /messages/:id/post
	 */
	public function action_post_post()
	{
		$message = $this->resource();

		if ($message->direction !== 'incoming')
		{
			throw HTTP_Exception::factory(400, 'Posts can only be created from incoming messages.');
		}

		if ($message->post_id !== NULL)
		{
			throw HTTP_Exception::factory(400, 'Post already exists for this message.');
		}

		$uri = Route::get('api')->uri(array(
			'controller' => 'Posts'
		));

		$post_data = array(
			'title' => $message->title,
			'content' => $message->message,
			'status' => 'draft',
			'form' => 1,
			'locale' => 'en_us'
		);

		// Send a sub request to api/posts
		$response = Request::factory($uri)
			->headers($this->request->headers()) // Forward current request headers to the sub request
			->method(Request::POST)
			->body(json_encode($post_data))
			->execute();

		// Override response to ensure status code etc is set
		$this->response = $response;

		// Return a JSON formatted response
		$this->_response_payload  = json_decode($response->body(), TRUE);

		if ($response->status() == 200)
		{
			$message->post_id = $this->_response_payload['id'];
			$message->save();
		}
	}

	/**
	 * GET post created from message
	 *
	 * GET /messages/:id/post
	 */
	public function action_get_post()
	{
		$message = $this->resource();

		if ($message->post_id === NULL)
		{
			throw HTTP_Exception::factory(404, 'Post does not exist this message.');
		}

		$uri = Route::get('api')->uri(array(
			'controller' => 'Posts',
			'id' => $message->post_id
		));

		// Send a sub request to api/posts/:id
		$response = Request::factory($uri)
			->headers($this->request->headers()) // Forward current request headers to the sub request
			->execute();

		// Override response to ensure status code etc is set
		$this->response = $response;

		// Return a JSON formatted response
		$this->_response_payload  = json_decode($response->body(), TRUE);
	}
}
