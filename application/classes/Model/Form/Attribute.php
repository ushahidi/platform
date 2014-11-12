<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Form_Attributes
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Form_Attribute extends ORM implements Acl_Resource_Interface {
	/**
	 * An attribute belongs to one form_group
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = [
		'form_group' => []
	];

	protected $_serialize_columns = array('options', 'default');

	/**
	 * Reserved attribute keys to avoid confusion with Posts table columns
	 *
	 * @var array key names
	 */
	protected $_reserved_keys = array(
		'slug',
		'type',
		'title',
		'content',
		'created',
		'updated',
		'email',
		'author',
		'form_id',
		'parent_id',
		'user_id',
		'status',
		'id',
		'tags',
		'values'
	);

	/**
	 * Available attribute types
	 * @var array
	 */
	protected static $_attribute_types = array(
		'datetime',
		'decimal',
		'geometry',
		'int',
		'point',
		'text',
		'varchar'
	);

	/**
	 * Getter for $_attribute_types
	 * @return array available attribute types
	 */
	public static function attribute_types()
	{
		return self::$_attribute_types;
	}

	/**
	 * Filters for the Post model
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'key' => array(
				array('trim'),
				// Make sure we have a URL-safe title.
				array('URL::title', array(':value', '_'))
			),
		);
	}

	/**
	 * Rules for the form_attribute model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('digit')
			),

			'form_id' => array(
				array('digit'),
			),
			'form_group_id' => array(
				array('digit'),
			),
			'key' => array(
				array('max_length', array(':value', 150)),
				array('alpha_dash', array(':value', TRUE)),
				array(array($this, 'unique'), array(':field', ':value')),
				array(array($this, 'not_reserved'), array(':field', ':value'))
			),
			'label' => array(
				array('not_empty'),
				array('max_length', array(':value', 150))
			),
			'input' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'text',
					'textarea',
					'select',
					'radio',
					'checkbox',
					'checkboxes',
					// todo: Backbone.Form doesn't have a File input, and this is done via media uploads.
					//       Do we drop this entirely in favor of media uploads, or ... ?
					// 'file',
					'date',
					'datetime',
					'location',
					'number'
				)) )
			),
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'decimal',
					'int',
					'geometry',
					'text',
					'varchar',
					'point',
					'datetime',
					'link'
				)) )
			),
			'required' => array(
				array('in_array', array(':value', array(true,false)))
			),
			'priority' => array(
				array('digit')
			),
			'cardinality' => array(
				array('digit')
			)
		);
	}

	/**
	 * Callback function to check if field key is reserved
	 */
	public function not_reserved($field, $value)
	{
		return ! in_array($field, $this->_reserved_keys);
	}

	/**
	 * Callback function to generate key if none set
	 */
	protected function _generate_key_if_empty()
	{
		if (empty($this->key))
		{
			$this->key = $this->label;

			// FIXME horribly inefficient
			// If the key exists add a count to the end
			$i = 1;
			while (! $this->unique('key', $this->key))
			{
				$this->key = $this->key." $i";
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
		$this->_generate_key_if_empty();

		return parent::save($validation);
	}

	/**
	 * Prepare attribute data for API
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
				'url' => Ushahidi_Api::url('attributes', $this->id),
				'key' => $this->key,
				'label' => $this->label,
				'input' => $this->input,
				'type' => $this->type,
				'required' => ($this->required) ? TRUE : FALSE,
				'default' => $this->default,
				'priority' => $this->priority,
				'options' => $this->options,
				'cardinality' => $this->cardinality
			);
		}
		else
		{
			$response = array(
				'errors' => array(
					'Attribute does not exist'
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
		return 'form_attributes';
	}
}
