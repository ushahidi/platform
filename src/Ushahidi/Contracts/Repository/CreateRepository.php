<?php

/**
 * Ushahidi Platform Create Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\EntityGet;

interface CreateRepository extends EntityGet
{
    /**
     * Creates a new record and returns the created id.
     * @param  array|\Ushahidi\Contracts\Entity $entity
     * @return mixed
     */
    public function create(Entity $entity);
}
