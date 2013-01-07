<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Forms
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

class Model_Form extends ORM {
	/**
	 * A form has many attributes and groups
	 * A form has many [children] forms
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'form_attributes' => array(),
		'form_groups' => array(),

		'children' => array(
			'model'  => 'Form',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A form belongs to a user and a [parent] form
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'user' => array(),

		'parent' => array(
			'model'  => 'form',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * Rules for the form model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 150))
			),

			// Form Types
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'report',
					'comment',
					'message',
					'alert'
				)) )
			)
		);
	}

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');
}
