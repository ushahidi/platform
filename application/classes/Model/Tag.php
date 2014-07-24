<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Tags
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Tag extends ORM implements Acl_Resource_Interface {
	/**
	 * A tag has and belongs to many posts
	 * A tag has many [children] tags
	 *
	 * @var array Relationships
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
	 * @var array Relationships
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

			'color' => array(
				// Remove # from start of color value
				array('ltrim', array(':value', '#'))
			)
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
			'id' => array(
				array('numeric')
			),

			'tag' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 200)),
				array(array($this, 'unique_tag_parent_type'), array(':field', ':value'))
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

			'color' => array(
				array('color')
			),

			'icon' => array(
				array('alpha_dash'),
			),
		);
	}

	/**
	 * Callback function to check if tag exists
	 */
	public function unique_tag_parent_type($field, $value)
	{
		$duplicate = ORM::factory('Tag')
			->where('tag', '=', $value)
			->where('parent_id', '=', $this->parent_id ? $this->parent_id : 0)
			->where('type', '=', $this->type)
			->where('id', '!=', $this->id ? $this->id : 0)
			->find();

		return ! $duplicate->loaded();
	}

	/**
	 * Callback function to check if tag parent exists
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
			$this->slug = $this->tag;

			// FIXME horribly inefficient
			// If the slug exists add a count to the end
			$i = 1;
			while (! $this->unique('slug', $this->slug))
			{
				$this->slug = $this->tag." $i";
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
				'url' => Ushahidi_Api::url('tags', $this->id),
				'parent' => empty($this->parent_id) ? NULL : array(
					'id' => $this->parent_id,
					'url' => Ushahidi_Api::url('tags', $this->parent_id)
				),
				'tag' => $this->tag,
				'slug' => $this->slug,
				'type' => $this->type,
				'color' => $this->color ? '#' . $this->color : null,
				'icon' => $this->icon,
				'description' => $this->description,
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

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'tags';
	}

}
