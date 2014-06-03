<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Sets Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_Sets_Posts extends Ushahidi_Api {

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'sets';

	/**
	 * Load resource object
	 *
	 * @return  void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'sets';

		// Check set exist
		$set_id = $this->request->param('set_id', 0);

		$set = ORM::factory('Set', $set_id);

		if ( ! $set->loaded())
		{
			throw new HTTP_Exception_404('Set does not exist. ID: \':id\'', array(
				':id' => $set_id
			));
		}

		$this->_resource = $set;

	}

	/**
	 * Get the request access method
	 *
	 * Overriding this method to allow POST/DELETE methods to be
	 * PUT method because add/remove is equivalent to edit on the
	 * set
	 *
	 * @return string
	 */
	protected function _get_access_method()
	{
		$method = parent::_get_access_method();
		if ($method === 'delete' OR $method === 'post')
		{
			$method = 'put';
		}
		return $method;
	}

	/**
	 * Add an existing post to a set
	 *
	 * POST /api/sets/:set_id/posts
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post_data = $this->_request_payload;

		// Add an existing post
		if ( ! empty($post_data['id']))
		{
			$posts = ORM::factory('Post', $post_data['id']);

			if ( ! $posts->loaded())
			{
				throw new HTTP_Exception_400('Post does not exist or is not in this set');
			}

			// Add to set (if not already)
			if ( ! $this->resource()->has('posts', $posts))
			{
				$this->resource()->add('posts', $posts);
			}

			// Response is the complete post
			$this->_response_payload = $posts->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($posts);

		}
		else
		{
			throw new HTTP_Exception_400('No Post ID');
		}
	}

	/**
	 * Retrieve all posts attached to a set
	 *
	 * GET /api/sets/:set_id/posts
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{

		$set = $this->resource();

		$uri = Route::get('api')->uri(array(
			'set' => $set->id,
			'controller' => 'posts'
		));

		// Send a sub request to api/posts/:id
		$response = Request::factory($uri.URL::query(array('set' => $set->id)))
			->headers($this->request->headers()) // Forward current request headers to the sub request
			->execute();

		// Override response to ensure status code etc is set
		$this->response = $response;

		// Return a JSON formatted response
		$this->_response_payload  = json_decode($response->body());
	}

	/**
	 * Retrieve a post
	 *
	 * GET /api/sets/:set_id/posts/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		if ( ! $post_id = $this->request->param('id', 0))
		{
			throw new HTTP_Exception_400('No Post ID');
		}

		$set = $this->resource();


		$post = $set->posts
			->where('post_id', '=', $post_id)
			->where('set_id', '=', $set->id)
			->find();

		if ( ! $post->loaded())
		{
			throw new HTTP_Exception_404('Post does not exist or is not in this set. Post ID: \':id\'', array(
				':id' => $post_id,
			));
		}

		// Perhaps there is a better way to get to the api/posts/:id controller?
		$uri = Route::get('api')->uri(array(
			'id' => $post->id,
			'controller' => 'posts'
		));

		// Send a sub request to api/posts/:id
		$response = Request::factory($uri.URL::query()) // Forward query params
			->headers($this->request->headers()) // Forward current request headers to the sub request
			->execute();

		// Override response to ensure status code etc is set
		$this->response = $response;

		// Return a JSON formatted response
		$this->_response_payload  = json_decode($response->body());
	}


	/**
	 * Delete a single post
	 *
	 * DELETE /api/sets/:set_id/posts/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$this->_response_payload = array();

		if ( ! $post_id = $this->request->param('id', 0))
		{
			throw new HTTP_Exception_400('No Post ID');
		}

		$set = $this->resource();


		$post = $set->posts
			->where('post_id', '=', $post_id)
			->where('set_id', '=', $set->id)
			->find();

		if ( ! $post->loaded())
		{
			throw new HTTP_Exception_404('Post does not exist or is not in this set. Post ID: \':id\'', array(
				':id' => $post_id,
			));
		}

		$set->remove('posts', $post);

		// Response is the complete post
		$this->_response_payload = $post->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods($post);

	}
}