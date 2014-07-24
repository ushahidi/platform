<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Post extends ORM implements Acl_Resource_Interface {
	/**
	 * A post has many comments decimal, geometry, int
	 * point, text, varchar, tasks
	 *
	 * A post has and belongs to many sets and tags
	 *
	 * A post has many [children] posts
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'post_comments' => array(),
		'post_decimal' => array(),
		'post_geometry' => array(),
		'post_int' => array(),
		'post_point' => array(),
		'post_text' => array(),

		'tasks' => array(),

		'sets' => array('through' => 'posts_sets'),
		'tags' => array('through' => 'posts_tags'),
		'media' => array('through' => 'posts_media'),
		
		'children' => array(
			'model'  => 'Post',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A post belongs to a user, a form and a [parent]
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'user' => array(),
		'form' => array(),

		'parent' => array(
			'model'  => 'post',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A post has zero or one message
	 * @var array Relationships
	 */
	protected $_has_one = array(
			'messages' => array()
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Filters for the Post model
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'slug' => array(
				array('trim'),
				// Make sure we have a URL-safe title.
				array('URL::title')
			),

			'locale' => array(
				array('trim'),
				array('UTF8::strtolower')
			),
		);
	}

	/**
	 * Rules for the post model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('numeric')
			),

			'form_id' => array(
				array('not_empty'),
				array('numeric'),
				array(array($this, 'fk_exists'), array('Form', ':field', ':value'))
			),

			'parent_id' => array(
				array('numeric'),
				array(array($this, 'parent_exists'), array(':field', ':value'))
			),

			'title' => array(
				array('max_length', array(':value', 150))
			),

			// Post Types
			// FIXME: pretty sure we don't want to lock this down to 3 states
			// What do these represent in reality?
			'status' => array(
				array('in_array', array(':value', array(
					'draft',
					'published',
					'pending'
				)) )
			),

			// Post Types
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'report',
					'revision',
					'comment',
					'translation',
					'alert'
				)) )
			),

			// Post slug
			'slug' => array(
				array('alpha_dash', array(':value', TRUE)),
				array('max_length', array(':value', 150)),
				array(array($this, 'unique_slug'), array(':field', ':value'))
			),

			// Post locale
			'locale' => array(
				array('not_empty'),
				array('max_length', array(':value', 5)),
				array('alpha_dash', array(':value', TRUE)),
				// @todo check locale is valid
				array(array($this, 'unique_locale'), array(':field', ':value'))
			),
		);
	}

	/**
	 * Callback function to check if parent exists
	 */
	public function parent_exists($field, $value)
	{
		// Skip check if parent is empty
		if (empty($value)) return TRUE;

		$parent = ORM::factory('Post')
			->where('id', '=', $value)
			->where('id', '!=', $this->id)
			->find();

		return $parent->loaded();
	}

	/**
	 * Check whether slug is unique for reports
	 * ignore for other post types
	 *
	 * @param   string   $field  the field to check for uniqueness
	 * @param   mixed    $value  the value to check for uniqueness
	 * @return  bool     whteher the value is unique
	 */
	public function unique_slug($field, $value)
	{
		// If this is a report - check uniqueness
		if ($this->type == 'report')
		{
			$model = ORM::factory($this->object_name())
				->where($field, '=', $value)
				->where('type', '=', 'report')
				->find();

			if ($this->loaded())
			{
				return ( ! ($model->loaded() AND $model->pk() != $this->pk()));
			}

			return ( ! $model->loaded());
		}

		// otherwise skip the check
		return TRUE;
	}

	/**
	 * Check locale is unique for each report
	 *
	 * @param   string   $field  the field to check for uniqueness
	 * @param   mixed    $value  the value to check for uniqueness
	 * @return  bool     whteher the value is unique
	 */
	public function unique_locale($field, $value)
	{
		// If this is a report - check uniqueness
		if ($this->type == 'translation')
		{
			// Is locale the same as parent?
			if ($this->parent->locale == $this->locale)
				return FALSE;

			// Check for other translations
			$model = ORM::factory($this->object_name())
				->where($field, '=', $value)
				->where('type', '=', 'translation')
				->where('parent_id', '=', $this->parent_id)
				->find();

			if ($this->loaded())
			{
				return ( ! ($model->loaded() AND $model->pk() != $this->pk()));
			}

			return ( ! $model->loaded());
		}

		// otherwise skip the check
		return TRUE;
	}

	/**
	 * Callback function to generate slug if none set
	 */
	public function generate_slug_if_empty()
	{
		if (empty($this->slug))
		{
			$this->slug = $this->title;

			// FIXME horribly inefficient
			// If the slug exists add a count to the end
			$i = 1;
			while (! $this->unique_slug('slug', $this->slug))
			{
				$this->slug = $this->title." $i";
				$i++;
			}
		}
	}

	/**
	 * Updates or Creates the record depending on loaded()
	 *
	 * @chainable
	 * @param  Validation $validation Validation object
	 * @return ORM
	 */
	public function save(Validation $validation = NULL)
	{
		$this->generate_slug_if_empty();

		return parent::save($validation);
	}

	/**
	 * Prepare single post for api ( ++ Hairy :) )
	 * along with values from attached tables
	 *
	 * @return array $response
	 * @todo the queries need some optimizing (EAV Fun)
	 */
	public function for_api()
	{
		$response = array();
		if ( $this->loaded() )
		{
			$response = array(
				'id' => $this->id,
				'url' => $this->url(),
				'parent' => empty($this->parent_id) ? NULL : array(
					'id' => $this->parent_id,
					'url' => Ushahidi_Api::url('posts', $this->parent_id)
				),
				'user' => empty($this->user_id) ? NULL : array(
					'id' => $this->user_id,
					'url' => Ushahidi_Api::url('users', $this->user_id)
				),
				'form' => empty($this->form_id) ? NULL : array(
					'id' => $this->form_id,
					'url' => Ushahidi_Api::url('forms', $this->form_id),
				),
				'title' => $this->title,
				'content' => $this->content,
				'status' => $this->status,
				'type' => $this->type,
				'slug' => $this->slug,
				'locale' => $this->locale,
				'created' => ($created = DateTime::createFromFormat('U', $this->created))
					? $created->format(DateTime::W3C)
					: $this->created,
				'updated' => ($updated = DateTime::createFromFormat('U', $this->updated))
					? $updated->format(DateTime::W3C)
					: $this->updated,
				'values' => array(),
				'tags' => array()
			);

			// Create the Super Union
			// @todo generalize this - how do plugins add other attribute types?
			$response['values'] = array();
			foreach (Model_Form_Attribute::attribute_types() as $type)
			{
				ORM::factory('Post_' . ucfirst($type))
					->load_values_for_post($this->id, $response['values']);
			}

			// Get tags
			foreach ($this->tags->find_all() as $tag)
			{
				// @todo use $tag->for_api() once thats built
				$response['tags'][] = array(
					'id' => $tag->id,
					'url' => Ushahidi_Api::url('tags', $tag->id)
				);
			}
		}
		else
		{
			$response = array(
				'errors' => array(
					'Post does not exist'
					)
				);
		}

		return $response;
	}

	public function url()
	{
		switch ($this->type)
		{
			case 'revision':
				return Ushahidi_Api::url('posts/'.$this->parent_id.'/revisions', $this->id);
				break;
			case 'translation':
				return Ushahidi_Api::url('posts/'.$this->parent_id.'/translations', $this->id);
				break;
			case 'report':
			default:
				// @todo maybe put 'updates' url as /post/:parent_id/updates/:id
				return Ushahidi_Api::url('posts', $this->id);
				break;
		}
	}

	public function revisions()
	{
		return $this->children->where('type', '=', 'revision');
	}

	public function comments()
	{
		return $this->children->where('type', '=', 'comments');
	}

	public function translations()
	{
		return $this->children->where('type', '=', 'translations');
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'posts';
	}
}
