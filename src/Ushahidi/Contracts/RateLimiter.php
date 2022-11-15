<?php

/**
 * Ushahidi Rate Limiter interface
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

interface RateLimiter
{
    /**
     * @param \Ushahidi\Contracts\Entity $entity
     * @throws \Exception
     */
    public function limit(Entity $entity);
}
