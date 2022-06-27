<?php

/**
 * Repository for User Setting
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface UserSettingRepository extends
    EntityGet,
    EntityExists
{

    /**
     * @param  int $form_id
     *
     * @return [Ushahidi\Contracts\Repository\Entity\UserSetting, ...]
     */
    public function getByUser($user_id);
}
