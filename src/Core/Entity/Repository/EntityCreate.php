<?php

/**
 * Repository for Creating Entities
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\Repository;

use Ushahidi\Core\Entity;

interface EntityCreate
{
    /**
     * Creates a new record and returns the created id.
     * @param  Entity $entity
     * @return Mixed
     */
    public function create(Entity $entity);
}
