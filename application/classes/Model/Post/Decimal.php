<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Decimal
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Post_Decimal extends ORM {
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

	// Table Name
	protected $_table_name = 'post_decimal';

	/**
	 * Rules for the post_decimal model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'post_id' => array(
				//array('not_empty'),
				array('numeric'),
			),
			'form_attribute_id' => array(
				//array('not_empty'),
				array('numeric'),
			),
			'value' => array(
				array('not_empty'),
				array('decimal')
			)
		);
	}
}