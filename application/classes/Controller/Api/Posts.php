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

		$_post = ORM::factory('Post');
		
		$this->create_or_update_post($_post, $post);
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
		
		$posts_query = ORM::factory('Post')
			->where('type', '=', $this->_type)
			->order_by($this->record_orderby, $this->record_order)
			->offset($this->record_offset)
			->limit($this->record_limit);
		
		if ($this->_parent_id)
		{
			$posts_query->where('parent_id', '=', $this->_parent_id);
		}

		// Prepare search params
		// @todo generalize this?
		$q = $this->request->query('q');
		if (! empty($q))
		{
			$posts_query->and_where_open();
			$posts_query->where('title', 'LIKE', "%$q%");
			$posts_query->or_where('content', 'LIKE', "%$q%");
			$posts_query->and_where_close();
		}
		
		$type = $this->request->query('type');
		if (! empty($type))
		{
			$posts_query->where('type', '=', $type);
		}
		$slug = $this->request->query('slug');
		if (! empty($slug))
		{
			$posts_query->where('slug', '=', $slug);
		}
		$form = $this->request->query('form');
		if (! empty($form))
		{
			$posts_query->where('form_id', '=', $form);
		}
		$user = $this->request->query('user');
		if (! empty($user))
		{
			$posts_query->where('user_id', '=', $user);
		}
		$locale = $this->request->query('locale');
		if (! empty($locale))
		{
			$posts_query->where('locale', '=', $locale);
		}
		
		// date chcks
		$created_after = $this->request->query('created_after');
		if (! empty($create_after))
		{
			$created_after = date('Y-m-d H:i:s', strtotime($create_after));
			$posts_query->where('created', '>=', $created_after);
		}
		$created_before = $this->request->query('created_before');
		if (! empty($created_before))
		{
			$created_before = date('Y-m-d H:i:s', strtotime($created_before));
			$posts_query->where('created', '<=', $created_before);
		}
		$updated_after = $this->request->query('updated_after');
		if (! empty($updated_after))
		{
			$updated_after = date('Y-m-d H:i:s', strtotime($updated_after));
			$posts_query->where('updated', '>=', $updated_after);
		}
		$updated_before = $this->request->query('updated_before');
		if (! empty($updated_before))
		{
			$updated_before = date('Y-m-d H:i:s', strtotime($updated_before));
			$posts_query->where('updated', '<=', $updated_before);
		}
		
		// Attributes
		// @todo optimize this - maybe iterate over query params instead
		$attributes = ORM::factory('Form_Attribute')->find_all();
		foreach($attributes as $attr)
		{
			$attr_filter = $this->request->query($attr->key);
			if (! empty($attr_filter))
			{
				$sub = DB::select('post_id')
					->from('post_'.$attr->type)
					->where('form_attribute_id', '=', $attr->id)
					->where('value', 'LIKE', "%$attr_filter%");
				$posts_query->join(array($sub, 'Filter_'.ucfirst($attr->type)), 'INNER')->on('post.id', '=', 'Filter_'.ucfirst($attr->type).'.post_id');
			}
		}
		
		$posts = $posts_query->find_all();

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

		$curr = URL::site($this->request->uri() . URL::query($params), $this->request);
		$next = URL::site($this->request->uri() . URL::query($next_params), $this->request);
		$prev = URL::site($this->request->uri() . URL::query($prev_params), $this->request);

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
		$post = ORM::factory('Post')
			->where('id', '=', $post_id)
			->where('type', '=', $this->_type);
		if ($this->_parent_id)
		{
			$post->where('parent_id', '=', $this->_parent_id);
		}
		$post = $post->find();

		if (! $post->loaded())
		{
			throw new HTTP_Exception_404('Post does not exist. ID: \':id\'', array(
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

		$_post = ORM::factory('Post')
			->where('id', '=', $post_id)
			->where('type', '=', $this->_type);
		if ($this->_parent_id)
		{
			$_post->where('parent_id', '=', $this->_parent_id);
		}
		$_post = $_post->find();

		if (! $_post->loaded())
		{
			throw new HTTP_Exception_404('Post does not exist. ID: \':id\'', array(
				':id' => $post_id,
			));
		}
		
		$this->create_or_update_post($_post, $post);
	}
	
	/**
	 * Save post, attributes and tags
	 * 
	 * @param Post_Model $post
	 * @param array $post_data
	 */
	protected function create_or_update_post($post, $post_data)
	{
		// Make form_id a string, avoid triggering 'changed' value
		$post_data['form_id'] = isset($post_data['form_id']) ? (String) $post_data['form_id'] : NULL;
		
		$post->values($post_data, array(
			'form_id', 'title', 'content', 'status', 'slug', 'email', 'author', 'locale'
			));
		$post->parent_id = $this->_parent_id;
		$post->type = $this->_type;
		
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base post data
			$post->check();

			// Does post have custom fields included?
			if ( isset($post_data['values']) )
			{
				// Yes, loop through and validate each value
				// to the form_attribute
				foreach ($post_data['values'] as $key => $value)
				{
					$attribute = ORM::factory('Form_Attribute')
						->join('form_groups_form_attributes', 'INNER')
							->on('form_attribute.id', '=', 'form_attribute_id')
						->join('form_groups', 'INNER')
							->on('form_groups_form_attributes.form_group_id', '=', 'form_groups.id')
						->where('form_id', '=', $post_data['form_id'])
						->where('key', '=', $key)
						->find();
					
					// Throw 400 if attribute doesn't exist
					if (! $attribute->loaded() )
					{
						throw new HTTP_Exception_400('Invalid attribute supplied. \':attr\'', array(
							':attr' => $key,
						));
					}

					$_value = ORM::factory('Post_'.ucfirst($attribute->type))
						->set('value', $value)
						->set('post_id', $post->id)
						->set('form_attribute_id', $attribute->id);
					$_value->check();
				}
			}

			// Does post have tags included?
			$tag_ids = array();
			if ( isset($post_data['tags']) )
			{
				// Yes, loop through and validate each tag
				foreach ($post_data['tags'] as $value)
				{
					$tag = ORM::factory('Tag')
						->where('tag', '=', $value)
						->find();
					
					// Auto create tags if it doesn't exist
					if (! $tag->loaded() )
					{
						$tag->tag = $value;
						$tag->slug = $value;
						$tag->type = 'category';
						$tag->check();
						$tag->save();
					}
					
					// Save tag id for later
					$tag_ids[] = $tag->id;
				}
			}

			// Validates ... so save
			$post->values($post_data, array(
				'form_id', 'title', 'content', 'status', 'slug', 'email', 'author', 'locale'
				));
			$post->parent_id = $this->_parent_id;
			$post->type = $this->_type;

			$post->save();
			
			// Did the post change?
			$saved = $post->saved();

			if ( isset($post_data['values']) )
			{
				foreach ($post_data['values'] as $key => $value)
				{
					$attribute = ORM::factory('Form_Attribute')
						->join('form_groups_form_attributes', 'INNER')
							->on('form_attribute.id', '=', 'form_attribute_id')
						->join('form_groups', 'INNER')
							->on('form_groups_form_attributes.form_group_id', '=', 'form_groups.id')
						->where('form_id', '=', $post->form_id)
						->where('key', '=', $key)
						->find();

					if ( $attribute->loaded() )
					{
						$_value = ORM::factory('Post_'.ucfirst($attribute->type))
							->where('post_id', '=', $post->id)
							->where('form_attribute_id', '=', $attribute->id)
							->find();
						
						$_value->post_id = $post->id;
						$_value->form_attribute_id = $attribute->id;
						$_value->value = $value;
						$_value->save();
						
						$saved = ($saved OR $_value->saved());
					}
				}
			}
				
			// Add tags to post (has to happen after post is saved)
			if (count($tag_ids) > 0 AND ! $post->has('tags', $tag_ids))
			{
				$post->remove('tags')->add('tags', $tag_ids);
				$saved = ($saved OR TRUE);
			}

			// Save revision
			// Check save was successful, and something actually changed
			if ($post->type != 'revision' AND $saved)
			{
				// Save Revision
				$new_revision = ORM::factory('Post');
				// @todo maybe just exclude some values, rather than have to modify this if schema changes
				$new_revision->values($post->as_array(), array(
					'form_id', 'user_id', 'slug', 'title', 'content', 'author', 'email', 'status', 'locale'
				));
				// @todo grab current user_id
				$new_revision->parent_id = $post->id;
				$new_revision->type = 'revision';
				$new_revision->save();
				
				// @todo copy attribute values too
				if ( isset($post_data['values']) )
				{
					foreach ($post_data['values'] as $key => $value)
					{
						$attribute = ORM::factory('Form_Attribute')
							->join('form_groups_form_attributes', 'INNER')
								->on('form_attribute.id', '=', 'form_attribute_id')
							->join('form_groups', 'INNER')
								->on('form_groups_form_attributes.form_group_id', '=', 'form_groups.id')
							->where('form_id', '=', $new_revision->form_id)
							->where('key', '=', $key)
							->find();
	
						if ( $attribute->loaded() )
						{
							$_value = ORM::factory('Post_'.ucfirst($attribute->type))
								->where('post_id', '=', $new_revision->id)
								->where('form_attribute_id', '=', $attribute->id)
								->find();
							
							$_value->post_id = $new_revision->id;
							$_value->form_attribute_id = $attribute->id;
							$_value->value = $value;
							$_value->save();
						}
					}
				}

			// Add tags to post (has to happen after post is saved)
			if (count($tag_ids) > 0)
			{
					$new_revision->remove('tags');
					$new_revision->add('tags', $tag_ids);
			}
			}

			// Response is the complete post
			$this->_response_payload = $post->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
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

		$post = ORM::factory('Post')
			->where('id', '=', $post_id)
			->where('type', '=', $this->_type);
		if ($this->_parent_id)
		{
			$post->where('parent_id', '=', $this->_parent_id);
		}
		$post = $post->find();

		$this->_response_payload = array();
		if ( $post->loaded() )
		{
			// Return the post we just deleted (provides some confirmation)
			$this->_response_payload = $post->for_api();
			$post->delete();
		}
		else
		{
			throw new HTTP_Exception_404('Post does not exist. ID: \':id\'', array(
				':id' => $post_id,
			));
		}
	}
}
