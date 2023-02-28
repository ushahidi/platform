<?php

/**
 * Repository for Form Roles
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface FormRoleRepository extends
    EntityGet,
    EntityExists
{

    /**
     * @param  int $form_id
     * @return \Ushahidi\Contracts\Entity[]
     */
    public function getByForm(int $form_id);

    /**
     * @param  int $role_id
     * @param  int $form_id
     * @return boolean
     */
    public function existsInFormRole(int $role_id, int $form_id);

    /**
     * @param  \Ushahidi\Contracts\Entity[]  $entities
     * @return \Ushahidi\Contracts\Entity[]
     */
    public function updateCollection(array $entities);
}
