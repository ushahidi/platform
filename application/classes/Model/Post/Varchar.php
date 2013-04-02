<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Varchar
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

class Model_Post_Varchar extends ORM {
	/**
	 * A post_varchar belongs to a post and form_attribute
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
	protected $_table_name = 'post_varchar';

	/**
	 * Rules for the post_varchar model
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
				array('max_length', array(':value', 255))
			)
		);
	}
}