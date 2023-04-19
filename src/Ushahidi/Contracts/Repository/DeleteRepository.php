<?php

/**
 * Ushahidi Platform Delete Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Repository\EntityGet;

interface DeleteRepository extends EntityGet
{

    /**
     * @param  \Ushahidi\Contracts\Entity $entity
     *
     * @return integer|void
     */
    public function delete(Entity $entity);
}
