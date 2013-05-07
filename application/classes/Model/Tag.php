<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Tags
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

class Model_Tag extends ORM {
	/**
	 * A tag has and belongs to many posts
	 * A tag has many [children] tags
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'posts' => array('through' => 'posts_tags'),

		'children' => array(
			'model' => 'Tag',
			'foreign_key' => 'parent_id'
			)
		);

	/**
	 * A tag belongs to a [parent] user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'parent' => array(
			'model'  => 'Tag',
			'foreign_key' => 'parent_id',
			),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);

	/**
	 * Filters for the Tag model
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
	 * Rules for the tag model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'tag' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 200)),
				array(array($this, 'unique'), array(':field', ':value'))
			),
			
			// Tag slug
			'slug' => array(
				array('alpha_dash', array(':value', TRUE)),
				array('max_length', array(':value', 200)),
				array(array($this, 'unique'), array(':field', ':value'))
			),

			// Tag Types
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'category',
					'status',
					// @todo add a type for free tagging? vs structured categories
				) ) )
			),
			
			'priority' => array(
				array('numeric')
			),

			'parent_id' => array(
				array('numeric'),
				array(array($this, 'parent_exists'), array(':field', ':value'))
			),
		);
	}

	/**
	 * Callback function to check if tag exists
	 */
	public function parent_exists($field, $value)
	{
		// Skip check if parent is empty
		if (empty($value)) return TRUE;
		
		$parent = ORM::factory('Tag')
			->where('id', '=', $value)
			->where('id', '!=', $this->id)
			->find();
		
		return $parent->loaded();
	}

	/**
	 * Callback function to generate slug if none set
	 */
	public function generate_slug_if_empty()
	{
		if (empty($this->slug))
		{
			$this->slug = URL::title($this->tag);
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
	 * Prepare form data for API, along with all its 
	 * groups and attributes
	 * 
	 * @return array $response - array to be returned by API (as json)
	 */
	public function for_api()
	{
		$response = array();
		if ( $this->loaded() )
		{
			$response = array(
				'id' => $this->id,
				'url' => URL::site('api/v'.Ushahidi_Api::version().'/tags/'.$this->id, Request::current()),
				'parent' => empty($this->parent_id) ? NULL : array(
					'id' => $this->parent_id,
					'url' => URL::site('api/v'.Ushahidi_Api::version().'/tags/'.$this->parent_id, Request::current())
				),
				'tag' => $this->tag,
				'slug' => $this->slug,
				'type' => $this->type,
				'priority' => $this->priority,
				'created' => ($created = DateTime::createFromFormat('U', $this->created))
					? $created->format(DateTime::W3C)
					: $this->created,
			);
		}
		else
		{
			$response = array(
				'errors' => array(
					'Tag does not exist'
					)
				);
		}

		return $response;
	}
}
