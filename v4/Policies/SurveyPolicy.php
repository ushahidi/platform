<?php

namespace v4\Policies;

use v4\Models\Survey;
use Ushahidi\App\Auth\GenericUser as User;

use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\ParentAccess;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Tool\Permissions\AclTrait;

class SurveyPolicy
{

    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses methods from several traits to check access:
    // - `ParentAccess` to check if the user can access the parent,
    // - `AdminAccess` to check if the user has admin access
    use AdminAccess, ParentAccess;

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
    public function index(User $user)
    {
        $this->user = $user;
        $this->isAllowed(null, 'read');
        return false;
    }
    public function isAllowed($entity, $privilege){
        $authorizer = service('authorizer.form');
        
        // These checks are run within the user context.
        $user = $this->user;
        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Allow role with the right permissions
        if ($this->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
            return true;
        }

        if ($this->isUserAdmin($user)) {
            return true;
        }

        // We check if the user has access to a parent form. This check has to be run
        // before public access is granted!
        // @CHECK: what is a parent form?????
        // if (!$this->isAllowedParent($entity, $privilege, $user)) {
        //     return false;
        // }

        // If a form is not disabled, then *anyone* can view it.
        // @TODO  how to do this for a index policy?
        // if ($privilege === 'read' && !$this->isFormDisabled($entity)) {
        //     return true;
        // }


        // All users are allowed to search forms.
        // @TODO should only do 'search' here. Do 'read' above in the isFormDisabled check
        if ($privilege === 'search' || $privilege === 'read') {
            return true;
        }

    }
    protected function getParent(Entity $entity){}
}