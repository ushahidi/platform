<?php

/**
 * Ushahidi Form Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\ParentAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\Acl as AccessControlList;
use Ushahidi\Contracts\Repository\Entity\FormRepository;

// The `FormAuthorizer` class is responsible for access checks on `Forms`
class FormAuthorizer implements Authorizer
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
    use AccessControlList;

    // It requires a `FormRepository` to load parent posts too.
    protected $form_repo;

    /**
     * @param FormRepository $form_repo
     */
    public function __construct(FormRepository $form_repo)
    {
        $this->form_repo = $form_repo;
    }

    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        // These checks are run within the user context.
        $user = $this->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // Allow role with the right permissions
        if ($this->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
            return true;
        }

        // Then we check if a user has the 'admin' role. If they do they're
        // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // We check if the user has access to a parent form. This check has to be run
        // before public access is granted!
        if (!$this->isAllowedParent($entity, $privilege, $user)) {
            return false;
        }

        // If a form is not disabled, then *anyone* can view it.
        if ($privilege === 'read' && !$this->isFormDisabled($entity)) {
            return true;
        }

        // All users are allowed to search forms.
        if ($privilege === 'search') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }

    /* ParentAccess */
    protected function getParent(Entity $entity)
    {
        // If the post has a parent_id, we attempt to load it from the `PostRepository`
        if ($entity->parent_id) {
            return $this->form_repo->get($entity->parent_id);
        }

        return false;
    }

    /**
     * Check if a form is disabled.
     * @param  Entity $entity
     *
     * @return boolean
     */
    protected function isFormDisabled(Entity $entity)
    {
        return (bool) $entity->disabled;
    }
}
