<?php

/**
 * Repository for Roles
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityGet;
use Ushahidi\Contracts\Repository\EntityExists;

interface RoleRepository extends
    EntityGet,
    EntityExists
{
    /**
     * @param  Array $roles
     * @return Boolean
     */
    public function doRolesExist(array $roles = null);

    /**
     * @param string $name
     * @return \Ushahidi\Contracts\Entity
     */
    public function getByName($name);
}
