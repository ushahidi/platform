<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Api_Posts extends Ushahidi_Api {

	/**
	 * @var int Post Parent ID
	 */
	protected $_parent_id = 0;

	/**
	 * @var string Post Type
	 */
	protected $_type = 'report';

	/**
	 * Create A Post
	 * 
	 * POST /api/posts
	 * 
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post = $this->_request_payload;
		
		$_post = ORM::factory('Post')->values($post);
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base post data
			$_post->check();

			// Does post have custom fields included?
			if ( isset($post['values']) )
			{
				// Yes, loop through and validate each value
				// to the form_attribute
				foreach ($post['values'] as $key => $value)
				{
					$attribute = ORM::factory('Form_Attribute')
						->where('form_id', '=', $post['form_id'])
						->where('key', '=', $key)
						->find();
					
					// Throw 400 if attribute doesn't exist
					if (! $attribute->loaded() )
					{
						throw new Http_Exception_400('Invalid attribute supplied. \':attr\'', array(
							':attr' => $key,
						));
					}

					$_value = ORM::factory('Post_'.ucfirst($attribute->type))->values(array(
						'value' => $value
						));
					$_value->check();
				}
			}

			// Validates ... so save
			$_post->values($post, array(
				'form_id', 'type', 'title', 'content', 'status'
				));
			$_post->status = (isset($post['status'])) ? $post['status'] : NULL;
			$_post->parent_id = $this->_parent_id;
			$_post->type = $this->_type;
			$_post->save();

			if ( isset($post['values']) )
			{
				foreach ($post['values'] as $key => $value)
				{
					$attribute = ORM::factory('Form_Attribute')
						->where('form_id', '=', $post['form_id'])
						->where('key', '=', $key)
						->find();

					if ( $attribute->loaded() )
					{
						$_value = ORM::factory('Post_'.ucfirst($attribute->type));
						$_value->post_id = $_post->id;
						$_value->form_attribute_id = $attribute->id;
						$_value->value = $value;
						$_value->save();
					}
				}
			}

			// Response is the complete post
			$this->_response_payload = $_post->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			// Error response
			$this->_response_payload = array(
				'errors' => Arr::flatten($e->errors('models'))
				);
		}
	}

	/**
	 * Retrieve All Posts
	 * 
	 * GET /api/posts
	 * 
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$posts = ORM::factory('Post')
			->order_by('created', 'ASC')
			->find_all();

		$count = $posts->count();

		foreach ($posts as $post)
		{
			$results[] = $post->for_api();
		}

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
		);
	}

	/**
	 * Retrieve A Post
	 * 
	 * GET /api/posts/:id
	 * 
	 * @return void
	 */
	public function action_get_index()
	{
		$post_id = $this->request->param('id', 0);

		// Respond with post
		$post = ORM::factory('Post', $post_id);
		$this->_response_payload = $post->for_api();
	}

	/**
	 * Update A Post
	 * 
	 * PUT /api/posts/:id
	 * 
	 * @return void
	 */
	public function action_put_index()
	{
		
	}

	/**
	 * Delete A Post
	 * 
	 * DELETE /api/posts/:id
	 * 
	 * @return void
	 */
	public function action_delete_index()
	{
		$post_id = $this->request->param('id', 0);
		$post = ORM::factory('Post', $post_id);
		if ( $post->loaded() )
		{
			$post->delete();
		}
	}
}
