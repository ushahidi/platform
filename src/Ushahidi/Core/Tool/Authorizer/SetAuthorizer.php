<?php

/**
 * Ushahidi Set Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Entity\Set;
use Ushahidi\Core\Concerns\Acl;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivateDeployment;

// The `SetAuthorizer` class is responsible for access checks on `Sets`
class SetAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses methods from several traits to check access:
    // - `OwnerAccess` to check if a user owns the set
    // - `AdminAccess` to check if the user has admin access
    use AdminAccess, OwnerAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    // if roles are available for this deployment.
    use Acl;

    protected function isVisibleToUser(Set $set, $user)
    {
        if ($set->role) {
            return in_array($user->role, $set->role);
        }

        // If no roles are selected, the Set is considered completely public.
        return true;
    }

    /* Authorizer */
    public function isAllowed(Entity $set, $privilege)
    {
        // These checks are run within the user context.
        $user = $this->getUser();

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        // First check whether there is a role with the right permissions
        if ($this->acl->hasPermission($user, Permission::MANAGE_SETS)) {
            return true;
        }

        // Then we check if a user has the 'admin' role. If they do they're
        // allowed access to everything (all entities and all privileges)
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // Non-admin users are not allowed to make sets featured
        if (in_array($privilege, ['create', 'update']) and $set->hasChanged('featured')) {
            return false;
        }

        // If the user is the owner of this set, they can do anything
        if ($this->isUserOwner($set, $user)) {
            return true;
        }

        // Check if the Set is only visible to specific roles.
        if ($this->isVisibleToUser($set, $user) and $privilege === 'read') {
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
}
