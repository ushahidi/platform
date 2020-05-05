<?php

namespace v4\Policies;

use Ushahidi\Core\Entity;
use v4\Models\Survey;
use Ushahidi\Core\Entity\Permission;
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
     * @param Survey $survey
     * @return bool
     */
    public function update(Survey $survey) {
        // we convert to a form entity to be able to continue using the old authorizers and classes.
        $form = new Entity\Form($survey->toArray());
        return $this->isAllowed($form, 'update');
    }

    /**
     * @param $entity
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($entity, $privilege){
        $authorizer = service('authorizer.form');

        // These checks are run within the user context.
        $user = $authorizer->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Allow role with the right permissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
            return true;
        }

        if ($this->isUserAdmin($user)) {
            return true;
        }

        // Before /v4 we would check if the user has access to a parent form. This check has to be run
        // before public access is granted... but parent forms aren't a thing
        // @IMPORTANT : parent forms are not a thing, they don't do anything, they don't exist.
        // I leave this here because it can be confusing otherwise.
        // if (!$this->isAllowedParent($entity, $privilege, $user)) {
        //     return false;
        // }

        // If a form is not disabled, then *anyone* can view it.
         if ($privilege === 'read' && !$this->isFormDisabled($entity)) {
             return true;
         }

        // All users are allowed to search forms.
        // @TODO should only do 'search' here. Do 'read' above in the isFormDisabled check
        if ($privilege === 'search') {
            return true;
        }

        return false;
    }

    /**
     * Check if a form is disabled.
     * @param  Entity $entity
     * @return Boolean
     */
    protected function isFormDisabled(Entity\Form $entity)
    {
        return (bool) $entity->disabled;
    }

}
