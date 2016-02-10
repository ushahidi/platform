<?php

/**
 * Ushahidi PermissionAccess Trait
 *
 * Implements Acl::hasPermission()
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\RoleRepository

trait PermissionAccess
{
	protected $repo;

	public function setRepo(RoleRepository $repo)
	{
		$this->repo = $repo;
	}
	 
	// Acl interface
	public function hasPermission(User $user, Array $permissions)
	{
		$role = $this->repo->getByName($user->role);

		// Does the user have all the permisions?
		$found = array_intersect($permissions, $role->permissions);

		return $found === $permissions;
	}
}
