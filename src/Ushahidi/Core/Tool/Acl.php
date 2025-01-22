<?php

/**
 * Ushahidi Acl
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Facade\Feature;
use Ushahidi\Contracts\Permission;
use Ushahidi\Contracts\Acl as AclInterface;
use Ushahidi\Contracts\Repository\Entity\RoleRepository;

class Acl implements AclInterface
{
    protected $role_repo;

    const DEFAULT_ROLES = [
        'user'  => [Permission::EDIT_OWN_POSTS]
    ];

    public function setRoleRepo(RoleRepository $role_repo)
    {
        $this->role_repo = $role_repo;

        return $this;
    }

    // Acl interface
    public function hasPermission(Entity $user, $permission)
    {
        // If the user has no role, they have no permissions
        if (!$user->role) {
            return false;
        }

        // Admin has all permissions
        // This is probably never actually run, but here just in case
        if ($user->role === 'admin') {
            return true;
        }

        // Don't check for permissions if we don't have the
        // roles feature enabled
        if (Feature::isEnabled('roles')) {
            return $this->customRoleHasPermission($user, $permission);
        } else {
            return $this->defaultHasPermission($user, $permission);
        }
    }
    protected function customRoleHasPermission(Entity $user, $permission)
    {
        $role = $this->role_repo->getByName($user->role);
        if (isset($role->permissions) && is_array($role->permissions)) {
            $permissions = array_map('strtolower', $role->permissions);

            // Does the user have the permission?
            return in_array(strtolower($permission), $permissions);
        }

        return false;
    }

    protected function defaultHasPermission(Entity $user, $permission)
    {
        $defaultRoles = static::DEFAULT_ROLES;
        $rolePermissions = isset($defaultRoles[$user->role]) ? $defaultRoles[$user->role] : [];

        // Does the user have the permission?
        return in_array($permission, $rolePermissions);
    }
}
