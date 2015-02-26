<?php

/**
 * Repository for Extant Entities
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\Repository;

interface EntityExists
{
    /**
     * @param  mixed $id
     * @return bool
     */
    public function exists($id);
}
