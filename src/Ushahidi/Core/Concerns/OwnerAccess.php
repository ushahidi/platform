<?php

/**
 * Ushahidi Owner Access Trait
 *
 * Gives objects one new method:
 * `isUserOwner(Entity $entity, User $user)`
 *
 * This checks if `$user` is the owner of `$entity`
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

use Ushahidi\Contracts\Entity;

trait OwnerAccess
{
    /**
     * Check if $user is the owner of $ownable
     *
     * @return boolean
     */
    public function isUserOwner(Entity $ownable, Entity $user)
    {
        // @todo ensure we always check the original user_id not the updated value!
        return ($user->getId() && $ownable->user_id === $user->getId());
    }

    /**
     * Check if $user and owner of $ownable are anonymous (user id 0)
     *
     * @return boolean
     */
    public function isUserAndOwnerAnonymous(Entity $ownable, Entity $user)
    {
        // @todo ensure we always check the original user_id not the updated value!
        return (! $user->getId() && ! $ownable->user_id);
    }
}
