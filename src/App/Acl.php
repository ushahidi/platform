<?php

/**
 * Ushahidi Acl
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App;

use Ushahidi\Core\Tool\Permissions\Acl as AclInterface;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Entity\RoleRepository;

class Acl implements AclInterface
{
	protected $role_repo;
	protected $roles_enabled = false;
	const DEFAULT_ROLES = [
		'user'  => [Permission::EDIT_OWN_POSTS]
	];

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
	public function hasPermission(User $user, $permission)
	{
		// If the user has no role, they have no permissions
		if (!$user->role) {
			return false;
		}

		// Don't check for permissions if we don't have the
		// roles feature enabled
		if ($this->hasRolesEnabled()) {
			return $this->customRoleHasPermission($user, $permission);
		} else {
			return $this->defaultHasPermission($user, $permission);
		}
	}

	protected function customRoleHasPermission(User $user, $permission)
	{
		$role = $this->role_repo->getByName($user->role);

		// Does the user have the permission?
		return in_array($permission, $role->permissions);
	}

	protected function defaultHasPermission(User $user, $permission)
	{
		// Admin has all permissions
		// This is probably never actually run, but here just in case
		if ($user->role === 'admin') {
			return true;
		}

		$defaultRoles = static::DEFAULT_ROLES;
		$rolePermissions = isset($defaultRoles[$user->role]) ? $defaultRoles[$user->role] : [];

		// Does the user have the permission?
		return in_array($permission, $rolePermissions);
	}
}
