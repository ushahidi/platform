<?php

/**
 * Ushahidi Data Provider Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Contracts\Authorizer;
use Ushahidi\Core\Contracts\Entity;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\Acl;

// The `DataProviderAuthorizer` class is responsible for access checks on `DataProvider` Entities
class DataProviderAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method
    use PrivAccess;

    // Check that the user has the necessary permissions
    // if roles are available for this deployment.
    use Acl;

    // Authorizer
    public function isAllowed(Entity $entity, $privilege)
    {
        // These checks are run within the user context.
        $user = $this->getUser();

        // Allow role with the right permissions
        if ($this->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
            return true;
        }

        // Allow admin access
        if ($this->isUserAdmin($user)) {
            return true;
        }

        return false;
    }
}
