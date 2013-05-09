<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for task
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Task extends ORM {
	/**
	 * A task has many [children]
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'children' => array(
			'model' => 'Task',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A task belongs to a post, an assignor and an assignee
	 * and a [parent] task
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'post' => array(),
		'parent' => array(
			'model'  => 'Task',
			'foreign_key' => 'parent_id',
			),
		'assignor' => array(
			'model' => 'User',
			'foreign_key' => 'assignor',
			),
		'assignee' => array(
			'model' => 'User',
			'foreign_key' => 'assignee',
			),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Rules for the task model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(

		);
	}
}