<?php

/**
 * Repository for queued notifications
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;
use Ushahidi\Contracts\Repository\CreateRepository;

interface NotificationQueueRepository extends
    EntityGet,
    EntityExists,
    CreateRepository
{
    /**
     * Get new notifications
     *
     * @param  int $limit
     * @return array
     */
    public function getNotifications($limit);
}
