<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Entity;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\ParentAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Contracts\Entity as EntityContract;

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
    use AccessControlList;
    use ParentAccess;
    use OwnerAccess;

    protected $user;

    // It requires a `FormRepository` to load parent posts too.
    protected $form_repo;

    // It requires a `FormRepository` to load parent posts too.
    protected $post_repo;

    /**
     *
     * @param  \Ushahidi\Modules\User  $user
     * @return bool
     */
    public function index()
    {
        $empty_form = new Entity\Form();
        return $this->isAllowed($empty_form, 'search');
    }

    private function getPostArray(Post $post)
    {
        $data = $post->toArray();
        unset($data["completed_stages"]);
        unset($data["enabled_languages"]);
        unset($data["post_content"]);
        unset($data["translations"]);
        unset($data["sets"]);
        unset($data["categories"]);
        return $data;
    }
    /**
     *
     * @param GenericUser $user
     * @param Post $post
     * @return bool
     */
    public function show(User $user, Post $post)
    {
        $post_entity = new Entity\Post($this->getPostArray($post));
        return $this->isAllowed($post_entity, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Post $post
     * @return bool
     */
    public function delete(User $user, Post $post)
    {
        $post_entity = new Entity\Post($this->getPostArray($post));
        return $this->isAllowed($post_entity, 'delete');
    }
    /**
     * @param Post $post
     * @return bool
     */
    public function update(User $user, Post $post)
    {
        $post_entity = new Entity\Post($this->getPostArray($post));
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        return $this->isAllowed($post_entity, 'update');
    }
    /**
     * @param Post $post
     * @return bool
     */
    public function patch(User $user, Post $post)
    {
        $post_entity = new Entity\Post($this->getPostArray($post));
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        return $this->isAllowed($post_entity, 'update');
    }
    /**
     * @param Post $post
     * @return bool
     */
    public function changeStatus(User $user, Post $post)
    {
        $post_entity = new Entity\Post($this->getPostArray($post));
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        return $this->isAllowed($post_entity, 'update');
    }

    /**
     * @param Post $post
     * @return bool
     */
    public function store(User $user, $form_id, $user_id)
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $post_entity = new Entity\Post(['form_id' => $form_id, 'user_id' => $user_id]);
        return $this->isAllowed($post_entity, 'create');
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
        if (($privilege != "delete") && $authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            return true;
        }

        if (($privilege === "delete") && ($authorizer->acl->hasPermission($user, Permission::DELETE_POSTS))) {
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
        if (!$this->isAllowedParent($entity, $privilege, $user)) {
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
        if ($privilege === 'read' && !$entity->getId()) {
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
        // they are allowed to edit  the post. They can't change the post status or
        // ownership but those are already checked above
        if ($this->isUserOwner($entity, $user)
            && in_array($privilege, ['update', 'lock'])
            && $authorizer->acl->hasPermission($user, Permission::EDIT_OWN_POSTS)
        ) {
            return true;
        }

        // If the user is the owner of this post & they have delete own posts permission
        // they are allowed to edit or delete the post.
        if ($this->isUserOwner($entity, $user)
            && ($privilege === "delete")
            && $authorizer->acl->hasPermission($user, Permission::DELETE_OWN_POSTS)
        ) {
            return true;
        }

        // If the user is the owner of this post they can always view the post
        if ($this->isUserOwner($entity, $user)
            && in_array($privilege, ['read'])
        ) {
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

    protected function getParent(EntityContract $entity)
    {
        // If the post has a parent_id, we attempt to load it from the `PostRepository`
        if ($entity->parent_id) {
            $parent = Post::find($entity->parent_id);
            return new Entity\Post($parent->toArray());
        }

        return false;
    }

    /* FormRole */
    protected function isFormRestricted(EntityContract $entity, $user)
    {
        // If the $entity->form_id exists and the $form->everyone_can_create is False
        // we check to see if the Form & Role Join exists in the `FormRoleRepository`

        if ($entity->form_id) {
            $form_repo = service('repository.form');
            $roles = $form_repo->getRolesThatCanCreatePosts($entity->form_id);

            if ($roles['everyone_can_create'] > 0) {
                return false;
            }

            if (is_array($roles['roles']) && in_array($user->role, $roles['roles'])) {
                return false;
            }
        }

        return true;
    }
    protected function isPostPublishedToUser(EntityContract $entity, $user)
    {
        if ($entity->status === 'published' && $this->isUserOfRole($entity, $user)) {
            return true;
        }
        return false;
    }

    protected function isUserOfRole(EntityContract $entity, $user)
    {
        if ($entity->published_to) {
            return in_array($user->role, $entity->published_to);
        }

        // If no visibility info, assume public
        return true;
    }
}
