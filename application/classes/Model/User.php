<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Users
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_User extends Model_A1_User_ORM implements Acl_Role_Interface, Acl_Resource_Interface {
	/**
	 * A user has many tokens and roles
	 * A user has many posts, post_comments, roles and sets
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'posts' => array(),
		'post_comments' => array(),
		'sets' => array(),
		'contacts' => array(),

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

	/**
	 * A user belongs to a role
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'role' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Filters for the Tag model
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return Arr::merge(
			parent::filters(),
			array(
				'username' => array(
					array('trim'),
				),
				'email' => array(
					array(array($this, 'emptyToNull'), array(':value'))
				),
				'realname' => array(
					array(array($this, 'emptyToNull'), array(':value'))
				),
			)
		);
	}

	/**
	 * Rules for the user model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('numeric')
			),

			'email' => array(
				array('Valid::email'),
				array(array($this, 'unique'), array(':field', ':value')),
			),

			//Real name of user
			'realname' => array(
				array('max_length', array(':value', 150)),
			),

			//username of user
			'username' => array(
				array('max_length', array(':value', 255)),
				array(array($this, 'unique'), array(':field', ':value')),
			),

			//password of user
			'password' => array(
				array('min_length', array(':value', 7)),
				array('max_length', array(':value', 72)), // Bcrypt max length is 72
				// NOTE: Password should allow ANY character at all. Do not limit to alpha numeric or alpha dash.
			)
		);

	}

	/**
	 * Allows a model use both email and username as unique identifiers for login
	 *
	 * @param   string  unique value
	 * @return  string  field name
	 */
	public function unique_key($value)
	{
		return Valid::email($value) ? 'email' : 'username';
	}

	/**
	 * Returns string identifier of the Role
	 *
	 * @return string
	 */
	public function get_role_id()
	{
		// If set, return user role
		if ($this->role) return $this->role;

		// If we have no role, but the user is actually loaded (ie. its a real user), return user role
		if ($this->loaded()) return Kohana::$config->load('a2.user_role');

		// Otherwise return logged out/guest role
		return Kohana::$config->load('a2.guest_role');
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'users';
	}

	/**
	 * emptyToNull callback for filters
	 * Replace empty value with null so that we save NULL into mysql db
	 *
	 * @param  string $value
	 * @return string|NULL
	 */
	public function emptyToNull($value)
	{
		if ($value === '')
		{
			return NULL;
		}
		return $value;
	}
}
