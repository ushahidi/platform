<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Ohanzee\Entities\Layer as OhanzeeLayer;
use Ushahidi\Modules\V5\Models\Layer as EloquentLayer;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class LayerPolicy
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


    public function index()
    {
        $empty_layer_entity = new OhanzeeLayer();
        return $this->isAllowed($empty_layer_entity, 'search');
    }

    public function show(User $user, EloquentLayer $layer)
    {
        $layer_entity = new OhanzeeLayer($layer->toArray());
        return $this->isAllowed($layer_entity, 'read');
    }

    public function delete(User $user, EloquentLayer $layer)
    {
        $layer_entity = new OhanzeeLayer($layer->toArray());
        return $this->isAllowed($layer_entity, 'delete');
    }

    public function update(User $user, EloquentLayer $layer)
    {
        // we convert to a Layer entity to be able to continue using the old authorizers and classes.
        $layer_entity = new OhanzeeLayer($layer->toArray());
        return $this->isAllowed($layer_entity, 'update');
    }


    public function store(User $user, EloquentLayer $layer)
    {
        // we convert to a layer_entity entity to be able to continue using the old authorizers and classes.
        $layer_entity = new OhanzeeLayer($layer->toArray());
        return $this->isAllowed($layer_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.layer');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Then we check if a user has the 'admin' role. If they do they're
        // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // If a post is active then *anyone* can view it.
        // Only an admin can view inactive layers or create/edit/update layers
        if ($user->getId() and $privilege === 'read' && $this->isLayerActive($entity)) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }

    protected function isLayerActive($entity)
    {
        // To check if a layer is active we just check the post 'active' flag
        if ($entity->active) {
            return true;
        }

        return false;
    }
}
