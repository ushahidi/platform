<?php

/**
 * Repository for User Setting
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;

interface UserSettingRepository extends
    EntityGet,
    EntityExists
{

    /**
     * @param  int $form_id
     * @return [Ushahidi\Core\Entity\UserSetting, ...]
     */
    public function getByUser($user_id);
}
