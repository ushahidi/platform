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

	private $set = NULL;

	/**
	 * Load resource object
	 *
	 * @return  void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'posts';

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

		$this->set = $set;
		// Get post
		if ($post_id = $this->request->param('id', 0))
		{

			$post = $set->posts
				->where('post_id', '=', $post_id)
				->where('set_id', '=', $set_id)
				->find();

			if ( ! $post->loaded())
			{
				throw new HTTP_Exception_404('Set Post does not exist. ID: \':id\'', array(
					':id' => $post_id,
				));
			}

			$this->_resource = $post;
		}
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

		$post = $this->_request_payload;

		// Add an existing post
		if ( ! empty($post['id']))
		{
			$posts = ORM::factory('Post', $post['id']);
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
		$results = array();

		$posts = $this->resource()->posts->find_all();

		$count = $posts->count();

		foreach ($posts as $post)
		{
			// Check if user is allowed to access this post
			if ($this->acl->is_allowed($this->user, $post, 'get'))
			{
				$results[] = $post->for_api();
			}
		}

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
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
		// Respond with set
		if ($this->set !== NULL)
		{
			// Perhaps there is a better way to get to the api/posts/:id controller?
			$uri = Route::get('api')->uri(array(
				'id' => $this->set->posts->find()->id,
				'controller' => 'posts'
			));
			// Send a sub request to api/posts/:id
			$request = Request::factory($uri);

			// Forward current request headers to the sub request
			$request->headers($this->request->headers());

			// Return a JSON formatted response
			$this->_response_payload  = json_decode($request->execute()->body(),TRUE);
		}
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
		$set_id = $this->request->param('set_id');

		$set = ORM::factory('Set', $set_id);

		if ( ! $set->loaded())
		{
			throw new HTTP_Exception_404('Invalid Form ID. \':id\'', array(
				':id' => $set_id
			));
		}

		$set->remove('posts',$this->resource());

		// Response is the complete post
		$this->_response_payload = $this->_resource->for_api();
	}
}