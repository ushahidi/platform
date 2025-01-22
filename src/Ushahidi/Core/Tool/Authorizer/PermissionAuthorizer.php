<?php

/**
 * Ushahidi Permission Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;

class PermissionAuthorizer implements Authorizer
{
    use UserContext;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // Check if user has Admin access
    use AdminAccess;

    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        // These checks are run within the user context.
        $user = $this->getUser();

        // Only allow admin access
        if ($this->isUserAdmin($user)
            and in_array($privilege, ['search', 'read'])) {
            return true;
        }

        return false;
    }
}
