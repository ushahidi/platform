<?php

namespace v4\Policies;

use Ushahidi\App\Auth\GenericUser as User;
use Ushahidi\Core\Entity;
use v4\Models\Survey;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\ParentAccess;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Tool\Permissions\AclTrait;

class PostPolicy
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
    use AclTrait;

    protected $user;

    // It requires a `FormRepository` to load parent posts too.
    protected $form_repo;

    /**
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function index()
    {
        $empty_form = new Entity\Form();
        return $this->isAllowed($empty_form, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Survey $survey
     * @return bool
     */
    public function show(User $user, Survey $survey)
    {
        $form = new Entity\Form($survey->toArray());
        return $this->isAllowed($form, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Survey $survey
     * @return bool
     */
    public function delete(User $user, Survey $survey)
    {
        $form = new Entity\Form($survey->toArray());
        return $this->isAllowed($form, 'delete');
    }
    /**
     * @param Survey $survey
     * @return bool
     */
    public function update(User $user, Survey $survey)
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $form = new Entity\Form($survey->toArray());
        return $this->isAllowed($form, 'update');
    }


    /**
     * @param Survey $survey
     * @return bool
     */
    public function store()
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $form = new Entity\Form();
        return $this->isAllowed($form, 'create');
    }
    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.post');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // First check whether there is a role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            return true;
        }

        // Then we check if a user has the 'admin' role. If they do they're
        // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // We check if the user has access to a parent post. This doesn't
        // grant them access, but is used to deny access even if the child post
        // is public.
        if (! $this->isAllowedParent($entity, $privilege, $user)) {
            return false;
        }

        // Non-admin users are not allowed to create posts for other users.
        // Post must be created for owner, or if the user is anonymous post must have no owner.
        if ($privilege === 'create'
            && !$this->isUserOwner($entity, $user)
            && !$this->isUserAndOwnerAnonymous($entity, $user)
        ) {
            return false;
        }

        // Non-admin users are not allowed to create posts for forms that have restricted access.
        if (in_array($privilege, ['create', 'update', 'lock'])
            && $this->isFormRestricted($entity, $user)
        ) {
            return false;
        }

        // All users are allowed to create and search posts.
        if (in_array($privilege, ['create', 'search'])) {
            return true;
        }

        // If a post is published, then anyone with the appropriate role can read it
        if ($privilege === 'read' && $this->isPostPublishedToUser($entity, $user)) {
            return true;
        }

        // If entity isn't loaded (ie. pre-flight check) then *anyone* can view it.
        if ($privilege === 'read' && ! $entity->getId()) {
            return true;
        }

        // Only admins or users with 'Manage Posts' permission can change status
        if ($privilege === 'change_status') {
            return false;
        }

        // Only admins or users with 'Manage Posts' permission can change the ownership of a post
        if ($entity->hasChanged('user_id')) {
            return false;
        }

        // If the user is the owner of this post & they have edit own posts permission
        // they are allowed to edit or delete the post. They can't change the post status or
        // ownership but those are already checked above
        if ($this->isUserOwner($entity, $user)
            && in_array($privilege, ['update', 'delete', 'lock'])
            && $this->acl->hasPermission($user, Permission::EDIT_OWN_POSTS)) {
            return true;
        }

        // If the user is the owner of this post they can always view the post
        if ($this->isUserOwner($entity, $user)
            && in_array($privilege, ['read'])) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }

    /**
     * Check if a form is disabled.
     * @param  Entity $entity
     * @return Boolean
     */
    protected function isFormDisabled(Entity\Post $entity)
    {
        return (bool) $entity->disabled;
    }
}
