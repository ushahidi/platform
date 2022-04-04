<?php

/**
 * Repository for Form Roles
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface FormRoleRepository extends
    EntityGet,
    EntityExists
{

    /**
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormRole, ...]
     */
    public function getByForm($form_id);

    /**
     * @param  int $role_id
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormRole, ...]
     */
    public function existsInFormRole($role_id, $form_id);

    /**
     * @param  [Ushahidi\Contracts\Repository\Entity\FormRole, ...]  $entities
     * @return [Ushahidi\Contracts\Repository\Entity\FormRole, ...]
     */
    public function updateCollection(array $entities);
}
