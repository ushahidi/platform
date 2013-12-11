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

			//First name of user
			'first_name' => array(
				array('max_length', array(':value', 150)),
			),

			//Last name of user
			'last_name' => array(
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
	 * Prepare user data for API
	 *
	 * @return array $response - array to be returned by API (as json)
	 */
	public function for_api()
	{
		$response = array();
		if ( $this->loaded() )
		{
			$response = array(
				'id' => $this->id,
				'url' => URL::site('api/v'.Ushahidi_Api::version().'/users/'.$this->id, Request::current()),
				'email' => $this->email,
				'first_name' => $this->first_name,
				'last_name' => $this->last_name,
				'username' => $this->username,
				'logins' => $this->logins,
				'last_login' => $this->last_login,
				'failed_attempts' => $this->failed_attempts,
				'last_attempt' => $this->last_attempt,
				'role' => $this->role,

				'created' => ($created = DateTime::createFromFormat('U', $this->created))
					? $created->format(DateTime::W3C)
					: $this->created,
				'updated' => ($updated = DateTime::createFromFormat('U', $this->updated))
					? $updated->format(DateTime::W3C)
					: $this->updated,
				);

		}
		else
		{
			$response = array(
				'errors' => array(
					'User does not exist'
					)
				);
		}

		return $response;
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
}
