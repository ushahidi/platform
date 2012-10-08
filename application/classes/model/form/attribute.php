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

class Model_Form_Attribute extends ORM
{
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
	 * Rules for the form_attribute model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'key' => array(
				array('not_empty'),
				array('max_length', array(':value', 150))
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
					'file'
				)) )
			),
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'datetime',
					'decimal',
					'int',
					'geometry',
					'text',
					'varchar',
					'point'
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
}