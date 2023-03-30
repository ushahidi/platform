<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Modules\V5\Models\Permissions;
use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\UserContext;

class PermissionsPolicy
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
        $empty_permissions = new Permissions();
        return $this->isAllowed($empty_permissions, 'search', $user);
    }

    /**
     * @param User $user
     * @param Permissions $permissions
     * @return bool
     */
    public function show(User $user, Permissions $permissions):bool
    {
        return $this->isAllowed($permissions, 'read', $user);
    }

    /**
     * @param User $user
     * @param Permissions $permissions
     * @return bool
     */
    public function delete(User $user, Permissions $permissions):bool
    {
        return $this->isAllowed($permissions, 'delete', $user);
    }
    /**
     * @param User $user
     * @param Permissions $permissions
     * @return bool
     */
    public function update(User $user, Permissions $permissions):bool
    {
        return $this->isAllowed($permissions, 'update', $user);
    }


    /**
     * @param User $user
     * @param Permissions $permissions
     * @return bool
     */
    public function store(User $user):bool
    {
        $permissions = new Permissions();
        return $this->isAllowed($permissions, 'create', $user);
    }

    /**
     * @param Permissions $permissions
     * @param string $privilege
     * @param user $user
     * @return bool
     */
    public function isAllowed($permissions, $privilege, $user = null):bool
    {

        $authorizer = service('authorizer.permission');
        $user = $authorizer->getUser();

      // These checks are run within the user context.
      //$user = $this->getUser();

      // Only allow admin access
        if ($this->isUserAdmin($user)
          && in_array($privilege, ['search', 'read'])) {
            return true;
        }

        return false;
    }
}
