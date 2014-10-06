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
		$repo   = service('repository.post');
		$parser = service('factory.parser')->get('posts', 'search');
		$format = service('factory.formatter')->get('posts', 'read');
		$authorizer = service('tool.authorizer.post');

		// this probably belongs in the parser, or should just return the
		// order/limit params as an array for the search call
		$this->_prepare_order_limit_params();

		$sorting = [
			'orderby' => $this->_record_orderby,
			'order' => $this->_record_order,
			'offset' => $this->_record_offset,
			'limit' => $this->_record_limit,
			'type' => $this->_type,
			'parent' => $this->_parent_id
		];
		$input = $parser($sorting + $this->request->query());

		$repo->setSearchParams($input);

		$posts = $repo->getSearchResults();
		$total = $repo->getSearchTotal();

		$results = [];
		foreach ($posts as $post)
		{
			// Check if user is allowed to access this post
			// @todo preload user entity, avoid multiple queries
			if ( $authorizer->isAllowed($post, 'read') )
			{
				$result = $format($post);
				// @todo check with authorizer instead
				$result['allowed_methods'] = $this->_allowed_methods($post->getResource());
				$results[] = $result;
			}
		}

		// Count actual results since they're filtered by access check
		$count = count($results);

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'total_count' => $total,
			'results' => $results,
			)
			+ $this->_get_paging_parameters();
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
		$repo   = service('repository.post');
		$format = service('factory.formatter')->get('posts', 'read');
		$id     = $this->request->param('id', 0);
		$post   = $repo->getByIdAndParent($id, $this->request->param('post_id'));
		$authorizer = service('tool.authorizer.post');

		if (!$post->id)
		{
			throw new HTTP_Exception_404('Post :id does not exist', array(
				':id' => $id,
			));
		}

		if (! $authorizer->isAllowed($post, 'read'))
		{
			throw HTTP_Exception::factory('403', 'You do not have permission to access post :post', array(
				':post' => $id
			));
		}

		$this->_response_payload = $format($post);
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
		$format = service('factory.formatter')->get('posts', 'update');
		$read_parser = service('factory.parser')->get('posts', 'read');
		$write_parser = service('factory.parser')->get('posts', 'update');
		$usecase = service('usecase.post.update');

		$request = $this->_request_payload;

		$read = [];
		$read['id'] = $this->request->param('id', NULL);
		$read['parent_id'] = $this->request->param('post_id', NULL);
		$read['locale'] = $this->request->param('locale', NULL);

		try
		{
			$write_data = $write_parser($this->_request_payload);
			$read_data = $read_parser($read);
			$post = $usecase->interact($read_data, $write_data);
		}
		catch (Ushahidi\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
		catch (Ushahidi\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}

		$this->_response_payload = $format($post);
		$this->_response_payload['updated_fields'] = $usecase->getUpdated();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
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
			elseif (! is_array($post_data['form']))
			{
				$post_data['form_id'] = $post_data['form'];
			}
		}

		$post->values($post_data, array(
			'form_id', 'title', 'content', 'status', 'slug', 'locale'
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
						$validation->rule('values.'.$key,
							function(Validation $validation, $field, $value)
							{
								$validation->error($field, 'cardinality');
							},
							array(':validation', ':field', ':value')
						);
					}

					foreach ($value as $k => $v)
					{
						// Add error if no value passed
						if (! is_array($v) OR ! isset($v['value']))
						{
							$validation->rule("values.$key.$k",
								function(Validation $validation, $field, $value)
								{
									$validation->error($field, 'value_array_invalid');
								},
								array(':validation', ':field', ':value')
							);
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
								$validation->rule("values.$key.$k",
									function(Validation $validation, $field, $value)
									{
										$validation->error($field, 'value_id_exists');
									},
									array(':validation', ':field', ':value')
								);
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
				throw new Validation_Exception($validation, 'Failed to validate post values');
			}

			// unpack user to get user_id
			if (isset($post_data['user']) AND ! is_array($post_data['user']))
			{
				$post_data['user'] = array('id' => $post_data['user']);
			}
			elseif (isset($post_data['user_id']))
			{
				$post_data['user'] = array('id' => $post_data['user_id']);
			}

			if (! isset($post_data['user']))
			{
				$post_data['user'] = NULL;
			}

			$user_validation = new Validation($post_data) ;
			$user = $this->save_post_user($post, $post_data['user'], $user_validation);
			if ($user_validation->check() === FALSE)
			{
				throw new Validation_Exception($user_validation, 'Failed to validation user');
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
		catch (Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->array->errors('api/posts')))
				));
		}
	}

	/**
	 * Save post user info
	 *
	 * @param Post_Model $post
	 * @param array $post_data
	 * @param Validation $validation
	 * @return FALSE|Model_User
	 */
	protected function save_post_user($post, $user_data, &$validation)
	{
		$user = FALSE;
		// Do we have user info (email or name or id)
		if ($user_data)
		{
			// Do we have a user id?
			if (
				! empty($user_data['id'])
				AND $user_data['id'] != $post->user_id
				)
			{
				if (
						// New post and current user id
						(! $post->loaded() AND $this->user->id == $user_data['id'])
						// Allowed to manually set user info
						OR $this->acl->is_allowed($this->user, $post, 'change_user')
					)
				{
					$user = ORM::factory('User', $user_data['id']);
					if (! $user->loaded())
					{
						$validation->rule('user',
							function(Validation $validation, $field, $value)
							{
								$validation->error($field, 'user_exists');
							},
							array(':validation', ':field', ':value')
						);
						return FALSE;
					}
				}
				else
				{
					$validation->rule('user',
						function(Validation $validation, $field, $value)
						{
							$validation->error($field, 'change_user_permission');
						},
						array(':validation', ':field', ':value')
					);
					return FALSE;
				}
			}
			// Do we have an email or name?
			elseif (
				! empty($user_data['email'])
				OR ! empty($user_data['realname'])
			)
			{
				if (
						// New post and anonymous user
						(! $post->loaded() AND ! $this->user->loaded())
						// Allowed to manually set user info
						OR $this->acl->is_allowed($this->user, $post, 'change_user')
					)
				{
					// Save new user
					// Make sure email is set to something
					$user_data['email'] = (! empty($user_data['email'])) ? $user_data['email'] : NULL;

					// Check if user was loaded
					// Note: if the email was used before but not registered (no username) we're going to overwrite name details
					if ($post->user_id)
					{
						$user = $post->user;
					}
					else
					{
						$user = ORM::factory('User')
							->where('email', '=', $user_data['email'])
							->find();
					}

					// If user is registered, throw error telling them to log in
					if ($user->loaded() AND $user->username)
					{
						$validation->rule('user',
							function(Validation $validation, $field, $value)
							{
								$validation->error($field, 'user_already_registered');
							},
							array(':validation', ':field', ':value')
						);
						return FALSE;
					}

					$user->values($user_data, array('email', 'realname'));

					// @todo add a setting for requiring email or not
					// $user_validation = Validation::factory($post_data['user']);
					// $user_validation->rule('email', 'not_empty');

					$user->check(/* $user_validation */);
				}
				else
				{
					// @todo fix the case where we end up here but submission actually included same values as before
					// Error
					$validation->rule('user',
						function(Validation $validation, $field, $value)
						{
							$validation->error($field, 'change_user_permission');
						},
						array(':validation', ':field', ':value')
					);
					return FALSE;
				}
			}
		}
		// Is this a new post from a logged in user?
		elseif (! $post->loaded() AND $this->user->loaded())
		{
			// Set current user as post owner
			$user = $this->user;
		}
		// Otherwise no user info

		return $user;
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
