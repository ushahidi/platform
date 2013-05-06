<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Tags
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

class Model_OAuth_RefreshToken extends ORM {

	/**
	 * Table primary key
	 * @var string
	 */
	protected $_primary_key = 'refresh_token';

	// Table Name
	protected $_table_name = 'oauth_refresh_tokens';

	/**
	 * A refresh token belongs to a client and a user
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
			'refresh_token' => array(
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
			'refresh_token' => array(
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
				array('alpha_dash'),
			),
		);
	}
}
