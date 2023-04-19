<?php

/**
 * Ushahidi CSV Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Entity\CSV;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\Acl as AccessControl;
use Ushahidi\Core\Facade\Feature;

class CSVAuthorizer implements Authorizer
{
    use UserContext;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // Check if user has Admin access
    use AdminAccess;

    // Check that the user has the necessary permissions
    // if roles are available for this deployment.
    use AccessControl;

    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        // Check if the user can import data first
        if (!Feature::isEnabled('data-import')) {
            return false;
        }

        // These checks are run within the user context.
        $user = $this->getUser();

        // Allow role with the right permissions
        if ($this->acl->hasPermission($user, Permission::DATA_IMPORT_EXPORT) or
            $this->acl->hasPermission($user, Permission::LEGACY_DATA_IMPORT)) {
            return true;
        }

        // Allow admin access
        if ($this->isUserAdmin($user)) {
            return true;
        }

        return false;
    }
}
