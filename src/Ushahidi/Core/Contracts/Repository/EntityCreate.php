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

namespace Ushahidi\Core\Contracts\Repository;

use Ushahidi\Core\Contracts\Entity;

interface EntityCreate
{
    /**
     * Creates a new record and returns the created id.
     * @param  array|\Ushahidi\Core\Contracts\Entity $entity
     * @return mixed
     */
    public function create(Entity $entity);
}
