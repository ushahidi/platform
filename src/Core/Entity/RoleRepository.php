<?php

/**
 * Repository for Roles
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;

interface RoleRepository extends
	EntityGet,
	EntityExists
{
	/**
	 * @param  Array $roles
	 * @return Boolean
	 */
	public function doRolesExist(Array $roles = null);

	/**
	 * @param String $name
	 * @return \Ushahidi\Core\Entity\Role
	 */
	public function getByName($name);
}
