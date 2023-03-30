<?php

/**
 * Ushahidi Platform Delete Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Contracts\Repository;

use Ushahidi\Core\Contracts\Entity;
use Ushahidi\Core\Contracts\Repository\EntityGet;

interface DeleteRepository extends EntityGet
{

    /**
     * @param  Entity $entity
     *
     * @return integer|void
     */
    public function delete(Entity $entity);
}
