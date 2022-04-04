<?php

/**
 * Ushahidi Platform Session
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

interface Session
{

    /**
     * Get the user entity
     *
     * @return Entity
     */
    public function getUser();

    /**
     * Override the user set in oauth / lumen auth layer
     * with something else.
     *
     * This is primarily used to when running background jobs in a user
     * context. ie. an export that needs to run with the same permissions
     * as a user who triggered it
     *
     * @param int $userId
     */
    public function setUser(int $userId);
}
