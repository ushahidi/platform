<?php

/**
 * Ushahidi Console Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\PrivAccess;
use Ushahidi\Core\Concerns\UserContext;

// The `ConsoleAuthorizer` class is responsible for access checks for console tasks
class ConsoleAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    // @todo refactor to avoid including this. CLI doesn't have a user context
    use UserContext;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method.
    use PrivAccess;

    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        // All console requests are authorized
        return true;
    }
}
