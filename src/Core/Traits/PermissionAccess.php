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

use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Entity\User;

trait PermissionAccess
{
	protected $role_repo;
	protected $roles_enabled = FALSE;

	public function setRoleRepo(RoleRepository $role_repo)
	{
		$this->role_repo = $role_repo;
	}

	public function setRolesEnabled($roles_enabled)
	{
		$this->roles_enabled = $roles_enabled;
	}

	/**
	 * Check if custom roles are enabled for this deployment
	 * @return boolean
	 */
	protected function hasRolesEnabled()
	{
		return (bool) $this->roles_enabled;
	}
 
	// Acl interface
	public function hasPermission(User $user, Array $permissions)
	{
		if (!$this->hasRolesEnabled()) {
			return false;
		}
		
		if (!$user->role) {
			return false;
		}
		
		$entity = $this->role_repo->getByName($user->role);

		// Does the user have all the permisions?
		$found = array_intersect($permissions, $entity->permissions);

		return $found === $permissions;
	}
}
