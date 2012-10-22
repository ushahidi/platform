<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Users
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

class Model_User extends Model_Auth_User {
	/**
	 * A user has many tokens and roles
	 * A user has many posts, post_comments, roles and sets 
	 * 
	 * @var array Relationhips
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
			'model' => 'task',
			'foreign_key' => 'assignor',
			),
		'assignees' => array(
			'model' => 'task',
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
		return array(
			
		);
	}
}