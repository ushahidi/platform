<?php

/**
 * Ushahidi Tos Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;

// The `TosAuthorizer` class is responsible for access checks on `Tos`
class TosAuthorizer implements Authorizer
{
// The access checks are run under the context of a specific user
    use UserContext;

    // To check whether the user has admin access
    use AdminAccess;

    // To check whether user owns the webhook
    use OwnerAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    // It uses `PrivateDeployment` to check whether a deployment is private
    use PrivateDeployment;

    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        // These checks are run within the user context.
        $user = $this->getUser();

        //if user is not actual user, but is in fact anonymous
        if (($privilege === 'search' || $privilege === 'create')
            && $this->isUserAndOwnerAnonymous($entity, $user)) {
            return false;
        }

        // Only logged in users have access if the deployment is private
        if (!$this->canAccessDeployment($user)) {
            return false;
        }

        if ($privilege === 'create') {
            return true;
        }

        if ($privilege === 'search') {
            return true;
        }

        if ($privilege === 'read' && $entity->user_id === $user->id) {
            return true;
        }

        // If no other access checks succeed, we default to denying access
        return false;
    }
}
