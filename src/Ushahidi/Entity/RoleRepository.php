<?php

/**
 * Repository for Roles
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface RoleRepository
{

	/**
	 * @param  String $name
	 * @return Ushahidi\Entity\Role
	 */
	public function get($name);

	/**
	 * @param  Array $roles
	 * @return Boolean
	 */
	public function doRolesExist(Array $roles = null);
}
