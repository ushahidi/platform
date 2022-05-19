<?php

/**
 * Ushahidi Config Authorizer
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\Acl as AccessControlList;

// The `HXLAuthorizer` class is responsible for access checks on `HXL` Entities
class HXLAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // Check that the user has the necessary permissions
    // if roles are available for this deployment.
    use AccessControlList;


    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        // These checks are run within the `User` context.
        $user = $this->getUser();
        if ($this->isUserAdmin($user) ||
            $this->acl->hasPermission($user, Permission::MANAGE_POSTS) ||
            $this->acl->hasPermission($user, Permission::DATA_IMPORT_EXPORT) ||
            $this->acl->hasPermission($user, Permission::LEGACY_DATA_IMPORT) ||
            $this->acl->hasPermission($user, Permission::MANAGE_SETTINGS)
        ) {
            return true;
        }
        // If no other access checks succeed, we default to denying access
        return false;
    }
}
