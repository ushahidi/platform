<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for OAuth clients
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Koauth
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
abstract class Koauth_Model_OAuth_Client extends ORM {

	/**
	 * Table primary key
	 * @var string
	 */
	protected $_primary_key = 'client_id';

	/**
	 * A client has many refresh tokens, authorization code and access tokens
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'refresh_tokens' => array(),
		'authorization_codes' => array(),
		'access_tokens' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	
	protected $_serialize_columns = array('grant_types');

	/**
	 * Filters for the Post model
	 * 
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'authorization_code' => array(
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
			'client_id' => array(
				array('not_empty'),
				array('alpha_dash'),
				array(array($this, 'unique'), array(':field', ':value')),
			),
			
			'client_secret' => array(
				array('not_empty'),
			),
			
			'redirect_uri' => array(
				array('url'),
			),
			
			'grant_types' => array(
				
			),
		);
	}
}
