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
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'posts' => array(),
		'post_comments' => array(),
		'roles' => array('through' => 'roles_users'),
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

	protected $_belongs_to = array(
		'user' => array()
		);

	/**
	 * Rules for the user model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'email' => array(
				array('numeric'),
				array(array($this, 'email_exists'), array(':field', ':value'))
			),
			
			//First name of user
			'first_name' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 255))
			),
			
			//Last name of user
			'last_name' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 255))
			),
			
			//username of user
			'username' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 255))
			),
			
			//password of user
			'username' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 255))
			)


		);
			
	}

	/**
	 * Callback function to check if email exists
	 */
	public function email_exists($field, $value)
	{
		$email = ORM::factory('User')
				->where('email', '=', $value)
				->find();

		return $email->loaded();
	}


	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

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
				'password' => $this->password,
				'logins' => $this->logins,

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

}
