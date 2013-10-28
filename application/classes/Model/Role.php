<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Roles
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
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