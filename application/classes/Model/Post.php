<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Posts
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Post extends ORM {
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
				array('not_empty'),
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
					'url' => URL::site('api/v'.Ushahidi_Api::version().'/posts/'.$this->parent_id, Request::current())
				),
				'user' => empty($this->user_id) ? NULL : array(
					'id' => $this->user_id,
					'url' => URL::site('api/v'.Ushahidi_Api::version().'/users/'.$this->user_id, Request::current())
				),
				'form' => empty($this->form_id) ? NULL : array(
					'id' => $this->form_id,
					'url' => URL::site('api/v'.Ushahidi_Api::version().'/forms/'.$this->form_id, Request::current()),
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
			$datetimes = DB::select('key', 'value', array('post_datetime.id', 'id'))
				->from('post_datetime')
				->join('form_attributes')
					->on('post_datetime.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$decimals = DB::select('key', 'value', array('post_decimal.id', 'id'))
				->union($datetimes)
				->from('post_decimal')
				->join('form_attributes')
					->on('post_decimal.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			// Load Geometry value as WKT
			$geometries = DB::select('key', array(DB::expr('AsText(`value`)'), 'value'), array('post_geometry.id', 'id'))
				->union($decimals)
				->from('post_geometry')
				->join('form_attributes')
					->on('post_geometry.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$ints = DB::select('key', 'value', array('post_int.id', 'id'))
				->union($geometries)
				->from('post_int')
				->join('form_attributes')
					->on('post_int.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$texts = DB::select('key', 'value', array('post_text.id', 'id'))
				->union($ints)
				->from('post_text')
				->join('form_attributes')
					->on('post_text.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$varchars = DB::select('key', 'value', array('post_varchar.id', 'id'))
				->union($texts)
				->from('post_varchar')
				->join('form_attributes')
					->on('post_varchar.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$results = $varchars->execute();

			$values_with_keys = array();
			foreach ($results as $result)
			{
				if (! isset($values_with_keys[$result['key']]))
				{
					$values_with_keys[$result['key']] = array();
				}
				// Save value and id in multi-value format.
				$values_with_keys[$result['key']][] = array(
					'id' => $result['id'],
					'value' => $result['value']
				);
				
				// First or single value for attribute
				if (! isset($response['values'][$result['key']]))
				{
					$response['values'][$result['key']] = $result['value'];
				}
				// Multivalue - use array instead
				else
				{
					$response['values'][$result['key']] = $values_with_keys[$result['key']];
				}
			}

			// Special handling for points
			// Load points through ORM to use special Geometry handling
			$points = ORM::factory('Post_Point')
				->select('key')
				->join('form_attributes')
					->on('post_point.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id)
				->find_all();

			foreach ($points as $point)
			{
				if (! isset($values_with_keys[$point->key]))
				{
					$values_with_keys[$point->key] = array();
				}
				// Save value and id in multi-value format.
				$values_with_keys[$point->key][] = array(
					'id' => $point->id,
					'value' => $point->value
				);
				
				// First or single value for attribute
				if (! isset($response['values'][$point->key]))
				{
					$response['values'][$point->key] = $point->value;
				}
				// Multivalue - use array instead
				else
				{
					$response['values'][$point->key] = $values_with_keys[$point->key];
				}
			}

			// Get tags
			foreach ($this->tags->find_all() as $tag)
			{
				// @todo use $tag->for_api() once thats built
				$response['tags'][] = array(
					'id' => $tag->id,
					'url' => URL::site('api/v'.Ushahidi_Api::version().'/tags/'.$tag->id, Request::current())
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
				return URL::site('api/v'.Ushahidi_Api::version().'/posts/'.$this->parent_id.'/revisions/'.$this->id, Request::current());
				break;
			case 'translation':
				return URL::site('api/v'.Ushahidi_Api::version().'/posts/'.$this->parent_id.'/translations/'.$this->id, Request::current());
				break;
			case 'report':
			default:
				// @todo maybe put 'updates' url as /post/:parent_id/updates/:id
				return URL::site('api/v'.Ushahidi_Api::version().'/posts/'.$this->id, Request::current());
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
}
