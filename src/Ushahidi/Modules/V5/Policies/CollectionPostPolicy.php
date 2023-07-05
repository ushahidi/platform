<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Ohanzee\Entities\SetPost as OhanzeeSetPost;
use Ushahidi\Modules\V5\Models\SetPost as EloquentSetPost;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;
use App\Bus\Query\QueryBus;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionByIdQuery;

class CollectionPostPolicy
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

    public function index()
    {
        $set_post_entity = new OhanzeeSetPost();
        return $this->isAllowed($set_post_entity, 'search');
    }

    public function show(User $user, EloquentSetPost $set_post)
    {
        $set_post_entity = new OhanzeeSetPost();
        $set_post_entity->setState($set_post->toArray());
        return $this->isAllowed($set_post_entity, 'read');
    }

    public function delete(User $user, EloquentSetPost $set_post)
    {
        $set_post_entity = new OhanzeeSetPost();
        $set_post_entity->setState($set_post->toArray());
        return $this->isAllowed($set_post_entity, 'delete');
    }

    public function store(User $user, EloquentSetPost $set_post)
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $set_post_entity = new OhanzeeSetPost();
        $set_post_entity->setState($set_post->toArray());
        return $this->isAllowed($set_post_entity, 'create');
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

        // First check whether there is a role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_SETS)) {
            return true;
        }

        // If the user is the owner of this set, they can do anything
        if ($this->isCollectionOwner($user, $entity->set_id)) {
            return true;
        }


        // All *logged in* users can create sets
        // if ($user->getId() and $privilege === 'create') {
        //     return true;
        // }

        // Finally, all users can search sets
        if ($privilege === 'search') {
            return true;
        }
        // If no other access checks succeed, we default to denying access
        return false;
    }

    private function isCollectionOwner($user, $collection_id)
    {
        $collection =  $this->queryBus->handle(new FetchCollectionByIdQuery($collection_id));
        if (($collection->user_id) && ($user) && ($user->id === $collection->user_id)) {
            return true;
        }
        return false;
    }
}
