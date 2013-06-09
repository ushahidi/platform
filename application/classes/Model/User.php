<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_User extends Model_Auth_User {
	/**
	 * A user has many tokens and roles
	 * A user has many posts, post_comments, roles and sets 
	 * 
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'posts' => array(),
		'post_comments' => array(),
		'roles' => array(
			'through' => 'roles_users'
			),
		'sets' => array(),

		// Task Assignor / Assignee relationship
		'assignors' => array(
			'model' => 'Task',
			'foreign_key' => 'assignor',
			),
		'assignees' => array(
			'model' => 'Task',
			'foreign_key' => 'assignee'
			),
	);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Rules for the user model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array_merge(parent::rules(), array(
			
		));
	}
}