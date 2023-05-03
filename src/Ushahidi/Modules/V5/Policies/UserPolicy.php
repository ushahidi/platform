<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity\User as StaticUser;
use Ushahidi\Modules\V5\Models\User as EloquentUser;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\Acl as AccessControlList;
use Ushahidi\Core\Tool\Acl;

class UserPolicy
{



    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // Check if user has Admin access
    use AdminAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    use AccessControlList;

    protected $user;

    public function index(User $user): bool
    {
        $empty_model_user = new StaticUser();
        return $this->isAllowed($empty_model_user, 'search');
    }

    public function show(User $user, EloquentUser $eloquentUser): bool
    {
        $entity = new StaticUser();
        $entity->setState($eloquentUser->toArray());
        return $this->isAllowed($entity, 'read');
    }

    public function delete(User $user, EloquentUser $eloquentUser): bool
    {
        $entity = new StaticUser();
        $entity->setState($eloquentUser->toArray());

        return $this->isAllowed($entity, 'delete');
    }
    /**
     * @param User $user
     * @param ModelUser $model_user
     * @return bool
     */
    public function update(User $user, EloquentUser $eloquentUser): bool
    {
        $entity = new StaticUser();
        $entity->setState($eloquentUser->toArray());

        return $this->isAllowed($entity, 'update');
    }

    public function store(User $user): bool
    {
        $entity = new StaticUser();
        return $this->isAllowed($entity, 'create');
    }

    public function isAllowed($entity, $privilege): bool
    {

        $authorizer = service('authorizer.user');
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        //User should not be able to delete self
        if ($privilege === 'delete' && $this->isUserSelf($entity->id, $user->id)) {
            return false;
        }

        // Role with the Manage Users permission can manage all users
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_USERS)) {
            return true;
        }

        // Admin user should be able to do anything - short of deleting self
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // User cannot change their own role
        if ('update' === $privilege
            && $this->isUserSelf($entity->id, $user->id)
            && $entity->hasChanged('role')
        ) {
            return false;
        }

        // Regular user should be able to update and read_full only self
        if ($this->isUserSelf($entity->id, $user->id)
            && in_array($privilege, ['update', 'read_full', 'read'])
        ) {
            return true;
        }

        // Users should always be allowed to register
        if ($privilege === 'register') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }

    private function isUserSelf($user_id, $loggedin_user_id)
    {
        return ((int) $user_id === (int) $loggedin_user_id);
    }
}
