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

		$this->_resource = 'sets_posts';

		// Check set exist
		$set_id = $this->request->param('set_id', 0);

		$set = ORM::factory('Set', $set_id);

		if ( ! $set->loaded())
		{
			throw new HTTP_Exception_404('Set does not exist. ID: \':id\'', array(
				':id' => $set_id
			));
		}

		$this->_resource = ORM::factory('Set');


		// Get post
		if ($post_id = $this->request->param('id', 0))
		{
			$post = ORM::factory('Post')
				->join('posts_sets', 'INNER')->on('posts_sets.set_id', '=', 'posts_sets.set_id')
				->where('id', '=', $post_id)
				->where('set_id', '=',$set_id)
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
	 * Create a new post
	 *
	 * POST /api/sets/:set_id/posts
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$set_id = $this->request->param('set_id');

		$post = $this->_request_payload;

		$set = ORM::factory('Set', $set_id);

		if ( ! $set->loaded())
		{
			throw new HTTP_Exception_404('Invalid Set ID. \':id\'', array(
				':id' => $set_id,
			));
		}

		// If we're trying to add an existing attribute
		if ( ! empty($post['id']))
		{
			$posts = ORM::factory('Post', $post['id']);

			if ( ! $posts->loaded())
			{
				throw new HTTP_Exception_400('Post does not exist. Post ID: \':id\'', array(
				':id' => $post['id'],
			));
			}

			// Add to group (if not already)
			if ( ! $set->has('posts', $posts))
			{
				$set->add('posts', $posts);
			}

			// Response is the complete form
			$this->_response_payload = $posts->for_api();

		}
	}

	/**
	 * Retrieve all posts
	 *
	 * GET /api/sets/:set_id/posts
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$set_id = $this->request->param('set_id');
		$results = array();

		$set = ORM::factory('Set', $set_id)->order_by('id', 'ASC');

		if ( ! $set->loaded())
		{
			throw new HTTP_Exception_404('Set does not exist. Set ID: \':id\'', array(
				':id' => $set_id,
			));
		}

		$posts = $set->posts->find_all();

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
		$set = $this->resource();

		// Respond with set
		$this->_response_payload =  $set->for_api();
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
		$id = $this->request->param('id');
		$set_id = $this->request->param('set_id');

		$post = ORM::factory('Set_Post')
			->where('set_id', '=', $set_id)
			->where('id', '=', $id)
			->find();

		$this->_response_payload = array();
		if ($post->loaded())
		{
			// Return the post we just deleted (provides some confirmation)
			$this->_response_payload = $post->for_api();
			$post->delete();
		}
		else
		{
			throw new HTTP_Exception_404('Post does not exist. Post ID: \':id\'', array(
				':id' => $id,
			));
		}
	}
}