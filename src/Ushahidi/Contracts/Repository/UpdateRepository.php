<?php

/**
 * Ushahidi Platform Update Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Repository\EntityGet;

interface UpdateRepository extends EntityGet
{
    /**
     * @param array|Entity $entity
     *
     * @return void
     */
    public function update(Entity $entity);

    /**
     * @param array|Entity[] $entities
     *
     * @return void
     */
    // public function updateCollection(array $entities);
}
