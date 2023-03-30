<?php

/**
 * Ushahidi Guest Access Trait
 *
 * Gives objects one new method:
 * `isUserGuest(User $user)`
 *
 * This checks if `$user` is not logged in.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

use Ushahidi\Core\Contracts\Entity;

trait GuestAccess
{
    /**
     * Check if $user is unloaded or has the "guest" role
     */
    protected function isUserGuest(Entity $user)
    {
        return (!$user->id || $user->role === 'guest');
    }
}
