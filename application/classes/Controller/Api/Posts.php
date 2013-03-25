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
	protected $_parent_id = NULL;

	/**
	 * @var string Post Type
	 */
	protected $_type = 'report';
	
	/**
	 * @var string Field to sort results by
	 */
	protected $record_orderby = 'created';
	
	/**
	 * @var string Direct to sort results
	 */
	protected $record_order = 'ASC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $record_allowed_orderby = array('id', 'created', 'title');

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

		// unpack form to get form_id
		if (isset($post['form']))
		{
			if (is_array($post['form']) AND isset($post['form']['id']))
			{
				$post['form_id'] = $post['form']['id'];
			}
			elseif (is_numeric($post['form']))
			{
				$post['form_id'] = $post['form'];
			}
		}

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

			// Does post have tags included?
			$tag_ids = array();
			if ( isset($post['tags']) )
			{
				// Yes, loop through and validate each tag
				foreach ($post['tags'] as $value)
				{
					$tag = ORM::factory('Tag')
						->where('tag', '=', $value)
						->find();
					
					// Auto create tags if it doesn't exist
					if (! $tag->loaded() )
					{
						$tag->tag = $value;
						$tag->slug = $value;
						$tag->type = 'tag';
						$tag->check();
						$tag->save();
					}
					
					// Save tag id for later
					$tag_ids[] = $tag->id;
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

			// Add tags to post (has to happen after post is saved)
			if (count($tag_ids) > 0)
			{
				$_post->add('tags', $tag_ids);
			}

			// Response is the complete post
			$this->_response_payload = $_post->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			// Error response
			$this->_response_payload = array(
				'errors' => implode(', ', Arr::flatten($e->errors('models')))
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

		$this->prepare_order_limit_params();

		$posts = ORM::factory('Post')
			->order_by($this->record_orderby, $this->record_order)
			->offset($this->record_offset)
			->limit($this->record_limit)
			->find_all();

		$count = $posts->count();

		foreach ($posts as $post)
		{
			$results[] = $post->for_api();
		}

		// Current/Next/Prev urls
		$params = array(
			'limit' => $this->record_limit,
			'offset' => $this->record_offset,
		);
		// Only add order/orderby if they're already set
		if ($this->request->query('orderby') OR $this->request->query('order'))
		{
			$params['orderby'] = $this->record_orderby;
			$params['order'] = $this->record_order;
		}

		$prev_params = $next_params = $params;
		$next_params['offset'] = $params['offset'] + $params['limit'];
		$prev_params['offset'] = $params['offset'] - $params['limit'];
		$prev_params['offset'] = $prev_params['offset'] > 0 ? $prev_params['offset'] : 0;

		$curr = url::site('api/v2/posts/'.url::query($params));
		$next = url::site('api/v2/posts/'.url::query($next_params));
		$prev = url::site('api/v2/posts/'.url::query($prev_params));

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results,
			'limit' => $this->record_limit,
			'offset' => $this->record_offset,
			'order' => $this->record_order,
			'orderby' => $this->record_orderby,
			'curr' => $curr,
			'next' => $next,
			'prev' => $prev,
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

		if (! $post->loaded())
		{
			throw new Http_Exception_404('Post does not exist. ID: \':id\'', array(
				':id' => $post_id,
			));
		}

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
		$post_id = $this->request->param('id', 0);
		$post = $this->_request_payload;

		// unpack form to get form_id
		if (isset($post['form']))
		{
			if (is_array($post['form']) AND isset($post['form']['id']))
			{
				$post['form_id'] = $post['form']['id'];
			}
			elseif (is_numeric($post['form']))
			{
				$post['form_id'] = $post['form'];
			}
		}

		$_post = ORM::factory('Post', $post_id)->values($post);

		if (! $_post->loaded())
		{
			throw new Http_Exception_404('Post does not exist. ID: \':id\'', array(
				':id' => $post_id,
			));
		}
		
		// Set post id to ensure sane response if form doesn't exist yet.
		$_post->id = $post_id;
		
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
						->where('form_id', '=', $_post->form_id)
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

			// Does post have tags included?
			$tag_ids = array();
			if ( isset($post['tags']) )
			{
				// Yes, loop through and validate each tag
				foreach ($post['tags'] as $value)
				{
					$tag = ORM::factory('Tag')
						->where('tag', '=', $value)
						->find();
					
					// Auto create tags if it doesn't exist
					if (! $tag->loaded() )
					{
						$tag->tag = $value;
						$tag->slug = $value;
						$tag->type = 'tag';
						$tag->check();
						$tag->save();
					}
					
					// Save tag id for later
					$tag_ids[] = $tag->id;
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
						->where('form_id', '=', $_post->form_id)
						->where('key', '=', $key)
						->find();

					if ( $attribute->loaded() )
					{
						$_value = ORM::factory('Post_'.ucfirst($attribute->type))
							->where('post_id', '=', $post_id)
							->where('form_attribute_id', '=', $attribute->id)
							->find();
						
						$_value->post_id = $_post->id;
						$_value->form_attribute_id = $attribute->id;
						$_value->value = $value;
						$_value->save();
					}
				}
				
				// Currently we just ignore existing values
				// @todo add a way to delete existing values
			}

			// Add tags to post (has to happen after post is saved)
			if (count($tag_ids) > 0)
			{
				$_post->remove('tags');
				$_post->add('tags', $tag_ids);
			}

			// Response is the complete post
			$this->_response_payload = $_post->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new Http_Exception_400('Validation Error: \':errors\'', array(
				'errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
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
		$this->_response_payload = array();
		if ( $post->loaded() )
		{
			// Return the post we just deleted (provides some confirmation)
			$this->_response_payload = $post->for_api();
			$post->delete();
		}
		else
		{
			throw new Http_Exception_404('Post does not exist. ID: \':id\'', array(
				':id' => $post_id,
			));
		}
	}
}
