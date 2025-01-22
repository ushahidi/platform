<?php

/**
 * Ushahidi User Setting Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\OwnerAccess;
use Ushahidi\Core\Concerns\PrivateDeployment;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

// The `UserAuthorizer` class is responsible for access checks on `Users`
class UserSettingAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // - `AdminAccess` to check if the user has admin access
    use AdminAccess, OwnerAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    // Check that the user has the necessary permissions
    use AccessControlList;

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

        // Regular user should be able to perform all actions on their own settings
        if ($this->isUserOwner($entity, $user)) {
            return true;
        }

        // Anyone can search, this is highly problematic because the results
        // are loaded and then filtered out based on the read priv
        if ($privilege === 'search') {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
