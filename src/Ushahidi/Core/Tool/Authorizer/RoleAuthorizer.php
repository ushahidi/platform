<?php

/**
 * Ushahidi Role Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Contracts\Entity;
use Ushahidi\Core\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\PrivAccess;

class RoleAuthorizer implements Authorizer
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

        if ($privilege === 'delete' && $entity->protected === true) {
            return false;
        }

        // Only allow admin access
        if ($this->isUserAdmin($user)) {
            return true;
        }

        if ($user->getId() and $privilege === 'read') {
            return true;
        }
        // All users are allowed to search forms.
        if ($user->getId() and $privilege === 'search') {
            return true;
        }

        return false;
    }
}
