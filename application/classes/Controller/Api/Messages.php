<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Messages Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Messages extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'messages';
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
		$endpoint = service('factory.endpoint')->get('messages', 'create');

		$this->_restful($endpoint, $this->_request_payload);
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
		$endpoint = service('factory.endpoint')->get('messages', 'search');

		$this->_restful($endpoint, $this->request->query());
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
		$endpoint = service('factory.endpoint')->get('messages', 'read');

		$this->_restful($endpoint, $this->request->param());

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
		$endpoint = service('factory.endpoint')->get('messages', 'update');

		$this->_restful($endpoint, $this->_request_payload + $this->request->param());
	}

	/**
	 * Create post from message
	 *
	 * POST /messages/:id/post
	 */
	public function action_post_post()
	{
		if (empty($this->_request_payload['form']))
		{
			throw HTTP_Exception::factory(400, 'Post can only be created from a message when the form is defined');
		}

		// @todo make this a proper use case
		$repo    = service('repository.message');
		$message = $repo->get($this->request->param('id'));

		if ($message->direction !== 'incoming')
		{
			throw HTTP_Exception::factory(400, 'Posts can only be created from incoming messages');
		}

		if ($message->post_id !== NULL)
		{
			throw HTTP_Exception::factory(400, 'Post already exists for this message');
		}

		$uri = Route::get('api')->uri(array(
			'controller' => 'Posts'
		));

		$post_data = array(
			'title'   => $message->title,
			'content' => $message->message,
			'created' => $message->created,
			'status'  => 'draft',
			'form'    => $this->_request_payload['form'],
			'locale'  => 'en_us'
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
			// @todo this is ugly and horrible
			$parser = service('factory.parser')->get('messages', 'update');
			$data   = $parser([
				'post_id' => $this->_response_payload['id'],
				'status'  => $message->status, // required, fixme
			]);
			$repo->update($message->id, $data);
		}
	}

	/**
	 * GET post created from message
	 *
	 * GET /messages/:id/post
	 */
	public function action_get_post()
	{
		// @todo make this a proper use case
		$message = service('repository.message')->get($this->request->param('id'));

		if ($message->post_id === NULL)
		{
			throw HTTP_Exception::factory(404, 'Post does not exist for this message');
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
