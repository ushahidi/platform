<?php

namespace v4\Policies;

use Ushahidi\App\Auth\GenericUser as User;
use Ushahidi\Core\Entity;
use v4\Models\Category;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\ParentAccess;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Tool\Permissions\AclTrait;

class CategoryPolicy
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

    // It requires a `TagRepository` to load parent posts too.
    protected $tag_repo;

    /**
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function index()
    {
        $empty_tag = new Entity\Tag();
        return $this->isAllowed($empty_tag, 'search');
    }

    /**
     *
     * @param GenericUser $user
     * @param Category $category
     * @return bool
     */
    public function show(User $user, Category $category)
    {
        $tag = new Entity\Tag($category->toArray());
        return $this->isAllowed($tag, 'read');
    }

    /**
     *
     * @param GenericUser $user
     * @param Category $category
     * @return bool
     */
    public function delete(User $user, Category $category)
    {
        $tag = new Entity\Tag($category->toArray());
        return $this->isAllowed($tag, 'delete');
    }
    /**
     * @param Category $category
     * @return bool
     */
    public function update(User $user, Category $category)
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $tag = new Entity\Tag($category->toArray());
        return $this->isAllowed($tag, 'update');
    }


    /**
     * @param Survey $survey
     * @return bool
     */
    public function store()
    {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $tag = new Entity\Tag();
        return $this->isAllowed($tag, 'create');
    }
    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege)
    {
        $authorizer = service('authorizer.tag');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // First check whether there is a role with the right permissions
        if ($this->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
            return true;
        }

        // Then we check if a user has the 'admin' role. If they do they're
        // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // isAllowParent is usually checked here in v3, but we do
        // it at the eloquent level instead

        // isUserOfRole is usually checked here in v3, but we do
        // it at the eloquent level instead

        if ($privilege === 'search') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
