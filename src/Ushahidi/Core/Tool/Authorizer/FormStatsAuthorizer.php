<?php

/**
 * Ushahidi Form Stats Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Contracts\Permission;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

// The `FormStageAuthorizer` class is responsible for access checks on `Forms`
class FormStatsAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses methods from several traits to check access:
    // - `ParentAccess` to check if the user can access the parent,
    // - `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    use AccessControlList;

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

        return false;
    }
}
