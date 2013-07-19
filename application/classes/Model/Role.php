<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Roles
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Role extends ORM {
	/**
	 * A role has many users
	 * 
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'users' => array(),
	);

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