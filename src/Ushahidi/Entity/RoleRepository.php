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
	 * @param  string $name
	 * @return \Ushahidi\Entity\Role
	 */
	public function get($name);
	
	/**
	 * @param string $name
	 * @return \Ushahidi\Entity\Role
	 */
	public function doRolesExist($name);

}


