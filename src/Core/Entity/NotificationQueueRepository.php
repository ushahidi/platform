<?php

/**
 * Repository for queued notifications
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;

interface NotificationQueueRepository extends
    EntityGet,
    EntityExists
{
	/**
	 * Get new notifications
	 *
	 * @param  int $limit
	 * @return array
	 */
	public function getNotifications($limit);
}
