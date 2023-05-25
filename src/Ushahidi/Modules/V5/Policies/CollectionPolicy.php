<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Set;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;
use Illuminate\Support\Facades\Auth;
use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionByIdQuery;

class CollectionPolicy
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
    // It uses `OwnerAccess` to provide  the `isUserOwner` method.
    use OwnerAccess;

    protected $user;


    private $queryBus;
    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     *
     * @return bool
     */
    public function index()
    {
        $set_entity = new Entity\Set();
        return $this->isAllowed($set_entity, 'search');
    }

    /**
     *
     * @param User $user
     * @param Set $set
     * @return bool
     */
    public function show(User $user, Set $set)
    {
        $set_entity = new Entity\Set();
        $set_entity->setState($set->toArray());
        return $this->isAllowed($set_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Set $set
     * @return bool
     */
    public function delete(User $user, Set $set)
    {
        $set_entity = new Entity\Set();
        $set_entity->setState($set->toArray());
        return $this->isAllowed($set_entity, 'delete');
    }
    /**
     * @param Set $set
     * @return bool
     */
    public function update(User $user, Set $set)
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $set_entity = new Entity\Set($set->toArray());
        return $this->isAllowed($set_entity, 'update');
    }


    /**
     * @param Survey $set
     * @return bool
     */
    public function store()
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $set_entity = new Entity\Set();
        return $this->isAllowed($set_entity, 'create');
    }
    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.set');

        // These checks are run within the user context.
        $user = $authorizer->getUser();
        //$user = Auth::user();
        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Then we check if a user has the 'admin' role. If they do they're
        // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }
        // Non-admin users are not allowed to make sets featured
        $old_set = Set::where('id', '=', $entity->id)->first();

        if (in_array($privilege, ['create', 'update'])
            && $this->valueIsChanged(
                'featured',
                $entity->featured,
                $old_set->toArray()
            )
        ) {
            return false;
        }

        // If the user is the owner of this set, they can do anything
        if ($this->isUserOwner($entity, $user)) {
            return true;
        }


        // First check whether there is a role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_SETS)) {
            return true;
        }


        // Check if the Set is only visible to specific roles.
        if ($this->isVisibleToUser($entity, $user) and $privilege === 'read') {
            return true;
        }

        // All *logged in* users can create sets
        if ($user->getId() and $privilege === 'create') {
            return true;
        }

        // Finally, all users can search sets
        if ($privilege === 'search') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }

    protected function isVisibleToUser(Entity\Set $entity, $user)
    {
        if ($entity->role) {
            return in_array($user->role, $entity->role);
        }

        // If no roles are selected, the Set is considered completely public.
        return true;
    }

    function valueIsChanged($key, $value, $old_values)
    {
        if ($value == $old_values[$key]) {
            return true;
        }
        return false;
    }
}
