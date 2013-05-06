<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Oauth Access Tokens
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Koauth
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
abstract class Koauth_Model_OAuth_AccessToken extends ORM {

	/**
	 * Table primary key
	 * @var string
	 */
	protected $_primary_key = 'access_token';

	// Table Name
	protected $_table_name = 'oauth_access_tokens';

	/**
	 * An access token belongs to a client and a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'client' => array(),
		'user' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);

	/**
	 * Filters for the Post model
	 * 
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'access_token' => array(
				array('trim'),
			),
		);
	}

	/**
	 * Rules for the post model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'access_token' => array(
				array('not_empty'),
				array('alpha_numeric'),
			),
			
			'client_id' => array(
				array('not_empty'),
				array('alpha_dash'),
			),
			
			'user_id' => array(
				array('numeric'),
			),
			
			'expires' => array(
				array('date'),
			),
			
			'scope' => array(
				//array('alpha_dash'),
			),
		);
	}
}
