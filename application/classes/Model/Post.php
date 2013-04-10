<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Posts
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
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
	 * @var array Relationhips
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
	 * @var array Relationhips
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
			'form_id' => array(
				array('not_empty'),
				array('numeric'),
				array(array($this, 'form_exists'), array(':validation', ':field', ':value'))
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
			
			// Post author
			'author' => array(
				array('max_length', array(':value', 150))
			),
			
			// Post email
			'email' => array(
				array('max_length', array(':value', 150)),
				array('email')
			),
		);
	}

	/**
	 * Callback function to check if form exists
	 */
	public function form_exists($validation, $field, $value)
	{
		$form = ORM::factory('Form')
			->where('id', '=', $value)
			->find();

		if ( ! $form->loaded() )
		{
			$validation->error($field, 'form_exists');
		}
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
	 * Callback function to generate slug if none set
	 */
	public function generate_slug_if_empty()
	{
		if (empty($this->slug))
		{
			$this->slug = URL::title($this->title);
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
				'url' => url::site('api/v'.Ushahidi_Api::version().'/posts/'.$this->id, Request::current()),
				'parent' => empty($this->parent_id) ? NULL : array(
					'id' => $this->parent_id,
					'url' => url::site('api/v'.Ushahidi_Api::version().'/posts/'.$this->parent_id, Request::current())
				),
				'user' => empty($this->user_id) ? NULL : array(
					'id' => $this->user_id,
					'url' => url::site('api/v'.Ushahidi_Api::version().'/users/'.$this->user_id, Request::current())
				),
				'form' => empty($this->form_id) ? NULL : array(
					'id' => $this->form_id,
					'url' => url::site('api/v'.Ushahidi_Api::version().'/forms/'.$this->form_id, Request::current()),
				),
				'title' => $this->title,
				'content' => $this->content,
				'status' => $this->status,
				'type' => $this->type,
				'email' => $this->email,
				'author' => $this->author,
				'slug' => $this->slug,
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
			$datetimes = DB::select('key', 'value')
				->from('post_datetime')
				->join('form_attributes')
					->on('post_datetime.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$decimals = DB::select('key', 'value')
				->union($datetimes)
				->from('post_decimal')
				->join('form_attributes')
					->on('post_decimal.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$geometries = DB::select('key', 'value')
				->union($decimals)
				->from('post_geometry')
				->join('form_attributes')
					->on('post_geometry.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$ints = DB::select('key', 'value')
				->union($geometries)
				->from('post_int')
				->join('form_attributes')
					->on('post_int.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$points = DB::select('key', 'value')
				->union($ints)
				->from('post_point')
				->join('form_attributes')
					->on('post_point.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$texts = DB::select('key', 'value')
				->union($points)
				->from('post_text')
				->join('form_attributes')
					->on('post_text.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$varchars = DB::select('key', 'value')
				->union($texts)
				->from('post_varchar')
				->join('form_attributes')
					->on('post_varchar.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);

			$datetimes = DB::select('key', 'value')
				->union($varchars)
				->from('post_datetime')
				->join('form_attributes')
					->on('post_datetime.form_attribute_id', '=', 'form_attributes.id')
				->where('post_id', '=', $this->id);
				
			$results = $datetimes->execute();

			foreach ($results as $result)
			{
				$response['values'][$result['key']] = $result['value'];
			}
			
			// Get tags
			foreach ($this->tags->find_all() as $tag)
			{
				// @todo use $tag->for_api() once thats built
				$response['tags'][] = $tag->tag;
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
