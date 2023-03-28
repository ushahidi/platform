<?php

/**
 * Ushahidi Platform Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

use Ushahidi\Contracts\Entity;

interface Authorizer
{
    /**
     * Get a list of the allowed privileges for a given entity.
     *
     * @return array
     */
    public function getAllowedPrivs(Entity $entity);

    /**
     * Check if access to an entity is allowed.
     *
     * @param  \Ushahidi\Contracts\Entity  $entity     Entity being accessed
     * @param  string  $privilege  Privilege that is requested
     * @return boolean
     */
    public function isAllowed(Entity $entity, $privilege);

    /**
     * Get the user for the current authorization context.
     *
     * @return \Ushahidi\Contracts\Entity
     */
    public function getUser();

    /**
     * Get the userid for the current authorization context.
     *
     * @return mixed
     */
    public function getUserId();
}
