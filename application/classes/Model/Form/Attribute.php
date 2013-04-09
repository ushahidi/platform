<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Form_Attributes
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

class Model_Form_Attribute extends ORM {
	/**
	 * A form_attribute belongs to a form
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'form' => array(),
		'form_group' => array(),
		);
		
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
	 * Rules for the form_attribute model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'form_id' => array(
				array('numeric'),
			),
			'form_group_id' => array(
				array('numeric'),
			),
			'key' => array(
				array('not_empty'),
				array('max_length', array(':value', 150)),
				array(array($this, 'is_unique'), array(':field', ':value')),
				array(array($this, 'not_reserved'), array(':validation', ':field', ':value'))
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
					'file',
					'date'
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
					'datetime'
				)) )
			),
			'required' => array(
				array('in_array', array(':value', array(true,false)))
			),
			'unique' => array(
				array('in_array', array(':value', array(true,false)))
			),
			'priority' => array(
				array('numeric')
			),
			'options' => array(
				array(array($this, 'valid_json'), array(':validation', ':field', ':value'))
			)
		);
	}

	/**
	 * Callback function to check if valid json
	 */
	public function valid_json($validation, $field, $value)
	{
		if ($value)
		{
			$json = json_encode($value);

			if ( $json === FALSE )
			{
				$validation->error($field, 'valid_json');
			}
		}
	}
	
	/**
	 * Callback function to check if key is unique
	 */
	public function is_unique($field, $value)
	{
		return ! (bool) DB::select(array(DB::expr('COUNT(*)'), 'total'))
			->from($this->_table_name)
			->where($field, '=', $value)
			->where('id', '!=', $this->id) // Exclude the report itself
			->execute()
			->get('total');
	}

	/**
	 * Callback function to check if field key is reserved
	 */
	public function not_reserved($validation, $field, $value)
	{
		if ( in_array($field, $this->_reserved_keys) )
		{
			$validation->error($field, 'reserved_key');
		}
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
				'url' => url::site('api/v2/forms/'.$this->form_id.'/attributes/'.$this->id, Request::current()),
				'form' => array(
					'url' => url::site('api/v2/forms/'.$this->form_id, Request::current()),
					'id' => $this->form_id
				),
				'form_group' => array(
					'url' => url::site('api/v2/forms/'.$this->form_id.'/groups/'.$this->form_group_id, Request::current()),
					'id' => $this->form_group_id
				),
				'key' => $this->key,
				'label' => $this->label,
				'input' => $this->input,
				'type' => $this->type,
				'required' => ($this->required) ? TRUE : FALSE,
				'default' => $this->default,
				'unique' => ($this->unique) ? TRUE : FALSE,
				'priority' => $this->priority,
				'options' => json_decode($this->options),
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
}