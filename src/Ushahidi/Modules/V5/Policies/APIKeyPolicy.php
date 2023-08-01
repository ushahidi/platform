<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Apikey;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class APIKeyPolicy
{

    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses methods from several traits to check access:
    // - `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    use AccessControlList;
    
    use OwnerAccess;

    protected $user;


    /**
     *
     * @param  \Ushahidi\Modules\User  $user
     * @return bool
     */
    public function index()
    {
        $empty_apikey_entity = new Entity\ApiKey();
        return $this->isAllowed($empty_apikey_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Apikey $apikey
     * @return bool
     */
    public function show(User $user, Apikey $apikey)
    {
        $apikey_entity = new Entity\ApiKey($apikey->toArray());
        return $this->isAllowed($apikey_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Apikey $apikey
     * @return bool
     */
    public function delete(User $user, Apikey $apikey)
    {
        $apikey_entity = new Entity\ApiKey($apikey->toArray());
        return $this->isAllowed($apikey_entity, 'delete');
    }
    /**
     * @param Apikey $apikey
     * @return bool
     */
    public function update(User $user, Apikey $apikey)
    {
        // we convert to a Apikey entity to be able to continue using the old authorizers and classes.
        $apikey_entity = new Entity\ApiKey($apikey->toArray());
        return $this->isAllowed($apikey_entity, 'update');
    }


    /**
     * @param Apikey $apikey
     * @return bool
     */
    public function store(User $user, Apikey $apikey)
    {
        // we convert to a apikey_entity entity to be able to continue using the old authorizers and classes.
        $apikey_entity = new Entity\ApiKey($apikey->toArray());
        return $this->isAllowed($apikey_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.apikey');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Role with the Manage Settings permission can have access
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
            return true;
        }

        // Admin is allowed access to everything
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
