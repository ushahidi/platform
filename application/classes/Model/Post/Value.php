<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Value
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

abstract class Model_Post_Value extends ORM {
	/**
	 * A post_decimal belongs to a post and form_attribute
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'post' => array(),
		'form_attribute' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);

	/**
	 * Rules for the post_decimal model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'post_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Post', ':field', ':value')),
			),
			'form_attribute_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Form_Attribute', ':field', ':value')),
			),
			'value' => array(
				
			)
		);
	}
}