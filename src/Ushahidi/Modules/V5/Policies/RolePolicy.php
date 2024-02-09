<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Modules\V5\Models\Role;
use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\UserContext;

class RolePolicy
{


    use UserContext;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // Check if user has Admin access
    use AdminAccess;

    protected $user;

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user):bool
    {
        $empty_role = new Role();
        return $this->isAllowed($empty_role, 'search', $user);
    }

    /**
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function show(User $user, Role $role):bool
    {
        return $this->isAllowed($role, 'read', $user);
    }

    /**
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function delete(User $user, Role $role):bool
    {
        return $this->isAllowed($role, 'delete', $user);
    }
    /**
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function update(User $user, Role $role):bool
    {
        return $this->isAllowed($role, 'update', $user);
    }


    /**
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function store(User $user):bool
    {
        $role = new Role();
        return $this->isAllowed($role, 'create', $user);
    }

    /**
     * @param Role $role
     * @param string $privilege
     * @param user $user
     * @return bool
     */
    public function isAllowed($role, $privilege, $user = null):bool
    {
        $authorizer = service('authorizer.role');
        $user = $authorizer->getUser();

        if ($privilege === 'delete' && $role->protected === true ) {
            return false;
        }

        // Only allow admin access
        if ($this->isUserAdmin($user)) {
            return true;
        }

        if ($user->getId() and $privilege === 'read') {
            return true;
        }
        // All users are allowed to search forms.
        if ($user->getId() and $privilege === 'search') {
            return true;
        }

        return false;
    }
}
