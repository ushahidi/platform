<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
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
	protected $_record_orderby = 'created';

	/**
	 * @var string Direct to sort results
	 */
	protected $_record_order = 'ASC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id', 'created', 'updated', 'title');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'posts';

	protected $_boundingbox = FALSE;

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		// Get dummy post for access check
		$this->_resource = ORM::factory('Post')
			->set('status', 'published');

		// Get parent if we have one
		if ($this->_parent_id = $this->request->param('post_id', NULL))
		{
			// Check parent post exists
			$parent = ORM::factory('Post', $this->_parent_id);
			if (! $parent->loaded())
			{
				throw new HTTP_Exception_404('Parent Post does not exist. Post ID: \':id\'', array(
					':id' => $this->_parent_id,
				));
			}

			// Use parent post for access check if no individual post set
			// This happens when getting all translations/revisions/updates..
			$this->_resource = $parent;
		}

		// Get post
		if ($post_id = $this->request->param('id', 0))
		{
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
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $post;
		}
	}

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

		$this->_prepare_order_limit_params();

		$posts_query = ORM::factory('Post')
			->distinct(TRUE)
			->where('type', '=', $this->_type)
			->order_by($this->_record_orderby, $this->_record_order);

		// set request
		// set param is set
		$set_id = $this->request->query('set');
		if (! empty($set_id))
		{
			$posts_query->join('posts_sets', 'INNER')
				->on('post.id', '=', 'posts_sets.post_id')
				->where('posts_sets.set_id', '=', $set_id);
		}

		if ($this->_record_limit !== FALSE)
		{
			$posts_query
				->limit($this->_record_limit)
				->offset($this->_record_offset);
		}

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
		// Filter on status, default status=published
		$status = $this->request->query('status');
		if (! empty($status))
		{
			if ($status != 'all')
			{
				$posts_query->where('status', '=', $status);
			}
		}
		else
		{
			$posts_query->where('status', '=', 'published');
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

		// Bounding box search
		// @todo eventually move this to Post_Point class?
		// Create geometry from bbox
		$bbox = $this->request->query('bbox');
		if (! empty($bbox))
		{
			$bbox = array_map('floatval', explode(',', $bbox));
			$bb_west = $bbox[0];
			$bb_north = $bbox[1];
			$bb_east = $bbox[2];
			$bb_south = $bbox[3];
			$this->_boundingbox = new Util_BoundingBox($bb_west, $bb_north, $bb_east, $bb_south);
		}

		if ($this->_boundingbox)
		{
			$sub = DB::select('post_id')
				->from('post_point')
				->where(
					DB::expr(
						'CONTAINS(GeomFromText(:bounds), value)',
						array(':bounds' => $this->_boundingbox->toWKT()) ),
					'=',
					1
				);
			$posts_query->join(array($sub, 'Filter_BBox'), 'INNER')->on('post.id', '=', 'Filter_BBox.post_id');
		}

		// Attributes
		// @todo optimize this - maybe iterate over query params instead
		$attributes = ORM::factory('Form_Attribute')->find_all();
		foreach ($attributes as $attr)
		{
			$attr_filter = $this->request->query($attr->key);
			if (! empty($attr_filter))
			{
				$table_name = ORM::factory('Post_'.ucfirst($attr->type))->table_name();
				$sub = DB::select('post_id')
					->from($table_name)
					->where('form_attribute_id', '=', $attr->id)
					->where('value', 'LIKE', "%$attr_filter%");
				$posts_query->join(array($sub, 'Filter_'.ucfirst($attr->key)), 'INNER')->on('post.id', '=', 'Filter_'.ucfirst($attr->key).'.post_id');
			}
		}

		// Filter by tag
		$tags = $this->request->query('tags');
		if (! empty($tags))
		{
			// Default to filtering to ANY of the tags.
			if (! is_array($tags))
			{
				$tags = array('any' => $tags);
			}

			if (isset($tags['any']))
			{
				$tags['any'] = explode(',', $tags['any']);
				$posts_query
					->join('posts_tags')->on('post.id', '=', 'posts_tags.post_id')
					->where('tag_id', 'IN', $tags['any']);
			}

			if (isset($tags['all']))
			{
				$tags['all'] = explode(',', $tags['all']);
				foreach ($tags['all'] as $tag)
				{
					$sub = DB::select('post_id')
						->from('posts_tags')
						->where('tag_id', '=', $tag);

					$posts_query
						->where('post.id', 'IN', $sub);
				}
			}
		}

		// Get the count of ALL records
		$count_query = clone $posts_query;
		$total_records = (int) $count_query
			->select(array(DB::expr('COUNT(DISTINCT `post`.`id`)'), 'records_found'))
			->limit(NULL)
			->offset(NULL)
			->find_all()
			->get('records_found');
		$count_query_sql = $count_query->last_query();

		// Get posts
		$posts = $posts_query->find_all();
		$post_query_sql = $posts_query->last_query();

		// Result count (for this request)
		$count = $posts->count();

		foreach ($posts as $post)
		{
			// Check if use is allowed to access this post
			if ($this->acl->is_allowed($this->user, $post, 'get'))
			{
				$result = $post->for_api();

				// @todo move this to 'meta' info
				$result['allowed_methods'] = $this->_allowed_methods($post);

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
		$prev_params['offset'] = ($prev_params['offset'] > 0) ? $prev_params['offset'] : 0;

		$curr = URL::site($this->request->uri().URL::query($params), $this->request);
		$next = URL::site($this->request->uri().URL::query($next_params), $this->request);
		$prev = URL::site($this->request->uri().URL::query($prev_params), $this->request);

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

		// Add debug info if environment isn't production
		if (Kohana::$environment !== Kohana::PRODUCTION)
		{
			$this->_response_payload['query'] = $post_query_sql;
			$this->_response_payload['count_query'] = $count_query_sql;
		}

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
		$post = $this->resource();

		$this->_response_payload = $post->for_api();

		// @todo move this to 'meta' info
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
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
		$post = $this->_request_payload;

		$_post = $this->resource();

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

		// unpack form to get form_id
		if (isset($post_data['form']))
		{
			if (is_array($post_data['form']) AND isset($post_data['form']['id']))
			{
				$post_data['form_id'] = $post_data['form']['id'];
			}
			elseif (is_numeric($post_data['form']))
			{
				$post_data['form_id'] = $post_data['form'];
			}
		}

		// unpack user to get user_id
		if (isset($post_data['user']))
		{
			if (is_array($post_data['user']) AND isset($post_data['user']['id']))
			{
				$post_data['user_id'] = $post_data['user']['id'];
			}
			elseif (is_numeric($post_data['user']))
			{
				$post_data['user_id'] = $post_data['user'];
			}
		}

		$post->values($post_data, array(
			'form_id', 'title', 'content', 'status', 'slug', 'locale', 'user_id'
			));
		$post->parent_id = $this->_parent_id;
		$post->type = $this->_type;

		// Validation object for additional validation (not in model)
		$validation = Validation::factory($post_data);
		// Validation - cycle through nested models
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base post data
			$post->check();

			// Does post have custom fields included?
			$_values = array();
			if (isset($post_data['values']))
			{
				// Yes, loop through and validate each value
				// to the form_attribute
				foreach ($post_data['values'] as $key => $value)
				{
					// Skip null/empty values
					if (empty($value))
						continue;

					$attribute = ORM::factory('Form_Attribute')
						->join('form_groups_form_attributes', 'INNER')
							->on('form_attribute.id', '=', 'form_attribute_id')
						->join('form_groups', 'INNER')
							->on('form_groups_form_attributes.form_group_id', '=', 'form_groups.id')
						->where('form_id', '=', $post_data['form_id'])
						->where('key', '=', $key)
						->find();

					// Throw 400 if attribute doesn't exist
					if (! $attribute->loaded())
					{
						throw new HTTP_Exception_400('Invalid attribute supplied. \':attr\'', array(
							':attr' => $key,
						));
					}

					// If we've got a complex value and just a single value (assuming complex values are associative arrays)
					// Handling exactly the same as a single value
					// @todo more complex handling ie. location + location name?
					if (ORM::factory('Post_'.ucfirst($attribute->type))->complex_value()
							AND is_array($value)
							AND (bool) count(array_filter(array_keys($value), 'is_string')))
					{
						$_value = ORM::factory('Post_'.ucfirst($attribute->type))
							->where('post_id', '=', $post->id)
							->where('form_attribute_id', '=', $attribute->id)
							->find();

						$_value
							->set('value', $value)
							->set('post_id', $post->id)
							->set('form_attribute_id', $attribute->id);
						$_value->check();

						// Add to array to save later
						$_values[] = $_value;

						continue;
					}

					// Handle single value
					if (! is_array($value))
					{
						$_value = ORM::factory('Post_'.ucfirst($attribute->type))
							->where('post_id', '=', $post->id)
							->where('form_attribute_id', '=', $attribute->id)
							->find();

						$_value
							->set('value', $value)
							->set('post_id', $post->id)
							->set('form_attribute_id', $attribute->id);
						$_value->check();

						// Add to array to save later
						$_values[] = $_value;

						continue;
					}

					// Are there multiple values? Are they greater than cardinality limit?
					if (is_array($value) AND count($value) > $attribute->cardinality AND $attribute->cardinality != 0)
					{
						$validation->error('values.'.$key, 'cardinality');
					}

					foreach ($value as $k => $v)
					{
						// Add error if no value passed
						if (! is_array($v) OR ! isset($v['value']))
						{
							$validation->error("values.$key.$k", 'value_array_invalid');
							continue;
						}

						// Skip empty/null values
						if (empty($v['value']))
							continue;

						// Load existing Post_* object
						if (! empty($v['id']))
						{
							$_value = ORM::factory('Post_'.ucfirst($attribute->type))
								->where('post_id', '=', $post->id)
								->where('form_attribute_id', '=', $attribute->id)
								->where('id', '=', $v['id'])
								->find();

							// Add error if id specified by doesn't exist
							if (! $_value->loaded())
							{
								$validation->error("values.$key.$k", 'value_id_exists');
							}
						}
						// Or get a new Post_* object
						else
						{
							$_value = ORM::factory('Post_'.ucfirst($attribute->type));
						}

						$_value
							->set('value', $v['value'])
							->set('post_id', $post->id)
							->set('form_attribute_id', $attribute->id);
						$_value->check();

						// Add to array to save later
						$_values[] = $_value;
					}
				}
			}

			// Validate required attributes
			$keys = (isset($post_data['values']) AND count($post_data['values']) > 0)
				? array_keys($post_data['values'])
				: array(0);
			$required_attributes = ORM::factory('Form_Attribute')
				->join('form_groups_form_attributes', 'INNER')
					->on('form_attribute.id', '=', 'form_attribute_id')
				->join('form_groups', 'INNER')
					->on('form_groups_form_attributes.form_group_id', '=', 'form_groups.id')
				->where('form_id', '=', $post_data['form_id'])
				->where('required', '=', 1)
				->where('key', 'NOT IN', $keys)
				->find_all();

			if ($required_attributes->count() > 0)
			{
				foreach ($required_attributes as $attr)
				{
					$validation->rule('values.'.$attr->key, 'not_empty');
				}
			}

			if ($validation->check() === FALSE)
			{
				throw new ORM_Validation_Exception('post_value', $validation);
			}

			// if name / email included with post
			$user = FALSE;
			if (isset($post_data['user'])
					AND is_array($post_data['user'])
					AND ! isset($post_data['user']['id'])
					AND (! empty($post_data['user']['email'])
						OR ! empty($post_data['user']['first_name'])
						OR ! empty($post_data['user']['last_name'])))
			{
				// Make sure email is set to something
				$post_data['user']['email'] = (! empty($post_data['user']['email'])) ? $post_data['user']['email'] : NULL;

				// Check if user was loaded
				$user = ORM::factory('User')
					->where('email', '=', $post_data['user']['email'])
					->find();
				if ($user->loaded() AND $user->username)
				{
					throw new HTTP_Exception_400('Email already registered, please log in to submit a report.');
				}

				$user->values($post_data['user'], array('email', 'first_name', 'last_name'));

				// @todo add a setting for requiring email or not
				// $user_validation = Validation::factory($post_data['user']);
				// $user_validation->rule('email', 'not_empty');

				$user->check(/* $user_validation */);
			}

			// Does post have tags included?
			$tag_ids = array();
			if (isset($post_data['tags']))
			{
				// Yes, loop through and validate each tag
				foreach ($post_data['tags'] as $value)
				{
					// Handle multiple formats
					// ID + URL array
					if (is_array($value) AND isset($value['id']))
					{
						$tag = ORM::factory('Tag')
						->where('id', '=', $value['id'])
						->find();
					}
					// Just ID
					elseif (is_numeric($value) AND intval($value) > 0)
					{
						$tag = ORM::factory('Tag')
						->where('id', '=', $value)
						->find();
					}
					// Tag or slug string
					else
					{
						$tag = ORM::factory('Tag')
						->where('slug', '=', $value)
						->or_where('tag', '=', $value)
						->find();
					}

					// Auto create tags if it doesn't exist
					if (! $tag->loaded())
					{
						$tag->tag = $value;
						$tag->type = 'category';
						$tag->check();
						$tag->save();
					}

					// Save tag id for later
					$tag_ids[] = $tag->id;
				}
			}

			// Save user
			if ($user)
			{
				$user->save();
				$post->user_id = $user->id;
			}

			// Validates ... so save
			$post->save();

			// Did the post change?
			$saved = $post->saved();

			// Save values
			$saved_value_ids = array();
			foreach ($_values as $_value)
			{
				$_value
					->set('post_id', $post->id)
					->save();
				// Save ID for deletion check later.
				$saved_value_ids[$_value->table_name()][] = $_value->id;
			}

			// Delete any old values that weren't passed through
			$db = Database::instance();
			foreach($saved_value_ids as $table => $_saved_value_ids)
			{
				DB::delete($table)
					->where('post_id', '=', $post->id)
					->where('id', 'NOT IN', $_saved_value_ids)
					->execute();
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
					'form_id', 'user_id', 'slug', 'title', 'content', 'status', 'locale'
				));
				// @todo grab current user_id
				$new_revision->parent_id = $post->id;
				$new_revision->type = 'revision';
				$new_revision->save();

				foreach ($_values as $post_value)
				{
					$_value = ORM::factory($post_value->object_name());
					$_value->post_id = $new_revision->id;
					$_value->form_attribute_id = $post_value->form_attribute_id;
					$_value->value = $post_value->value;
					$_value->save();
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
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($post);
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models')))
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
		$post = $this->resource();

		$this->_response_payload = array();
		if ($post->loaded())
		{
			// Return the post we just deleted (provides some confirmation)
			$this->_response_payload = $post->for_api();

			// @todo move this to 'meta' info
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();

			$post->delete();
		}
	}
}
