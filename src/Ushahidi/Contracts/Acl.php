<?php

/**
 * Ushahidi Platform Acl interface
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

interface Acl
{
    /**
     * Check if user has permissions
     *
     * @param \Ushahidi\Contracts\Entity $user
     * @param string $permission The permission to check for
     * @return boolean
     */
    public function hasPermission(Entity $user, $permission);
}
