<?php

/**
 * Ushahidi User Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Contracts\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\Acl;

// The `UserAuthorizer` class is responsible for access checks on `Users`
class UserAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // - `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    use Acl;

    /**
     * Get a list of all possible privilges.
     * By default, returns standard HTTP REST methods.
     * @return Array
     */
    protected function getAllPrivs()
    {
        return ['read', 'create', 'update', 'delete', 'search', 'read_full', 'register'];
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

        // User should not be able to delete self
        if ($privilege === 'delete' && $this->isUserSelf($entity)) {
            return false;
        }

        // Role with the Manage Users permission can manage all users
        if ($this->acl->hasPermission($user, Permission::MANAGE_USERS)) {
            return true;
        }

        // Admin user should be able to do anything - short of deleting self
        if ($this->isUserAdmin($user)) {
            return true;
        }

        // User cannot change their own role
        if ('update' === $privilege && $this->isUserSelf($entity) && $entity->hasChanged('role')) {
            return false;
        }

        // Regular user should be able to update and read_full only self
        if ($this->isUserSelf($entity) && in_array($privilege, ['update', 'read_full', 'read'])) {
            return true;
        }

        // Users should always be allowed to register
        if ($privilege === 'register') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
