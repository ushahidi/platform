<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for User_Task
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

class Model_User_Task extends ORM {
	/**
	 * A user_task has many [children]
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'children' => array(
			'model' => 'user_task',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A user_task belongs to a post, an assignor and an assignee
	 * and a [parent] user_task
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'post' => array(),
		'parent' => array(
			'model'  => 'user_task',
			'foreign_key' => 'parent_id',
			),
		'assignor' => array(
			'model' => 'user',
			'foreign_key' => 'assignor',
			),
		'assignee' => array(
			'model' => 'user',
			'foreign_key' => 'assignee',
			),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Rules for the user_task model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(

		);
	}
}