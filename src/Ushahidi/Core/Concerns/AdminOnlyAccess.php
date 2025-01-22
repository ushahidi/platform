<?php

/**
 * Ushahidi Attribute Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

trait AdminOnlyAccess
{
    // The access checks are run under the context of a specific user
    use UserContext;

    // It uses `AdminAccess` to check if the user has admin access
    use AdminAccess;

    // It uses `PrivAccess` to provide the `getAllowedPrivs` method
    use PrivAccess;

    /**
     * Allows full access only if the user is an admin
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return $this->isUserAdmin($this->getUser());
    }
}
