<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

class MediaPolicy
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
        $empty_media_entity = new Entity\Media();
        return $this->isAllowed($empty_media_entity, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Media $media
     * @return bool
     */
    public function show(User $user, Media $media)
    {
        $media_entity = new Entity\Media($media->toArray());
        return $this->isAllowed($media_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Media $media
     * @return bool
     */
    public function delete(User $user, Media $media)
    {
        $media_entity = new Entity\Media($media->toArray());
        return $this->isAllowed($media_entity, 'delete');
    }
    /**
     * @param Media $media
     * @return bool
     */
    public function update(User $user, Media $media)
    {
        // we convert to a Media entity to be able to continue using the old authorizers and classes.
        $media_entity = new Entity\Media($media->toArray());
        return $this->isAllowed($media_entity, 'update');
    }


    /**
     * @param Media $media
     * @return bool
     */
    public function store(User $user, Media $media)
    {
        // we convert to a media_entity entity to be able to continue using the old authorizers and classes.
        $media_entity = new Entity\Media($media->toArray());
        return $this->isAllowed($media_entity, 'create');
    }


    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.media');

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

        // All users are allowed to view and create new media files.
        if ($user->getId() and in_array($privilege, ['search'])) {
            return true;
        }

        if (in_array($privilege, ['read', 'create', 'search'])) {
            return true;
        }

        // Owners can removed media they own.
        if ($this->isUserOwner($entity, $user) && $privilege === 'delete') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
