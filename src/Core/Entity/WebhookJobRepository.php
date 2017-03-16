<?php

/**
 * Repository for webhooks jobs
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;

interface WebhookJobRepository extends
    EntityGet,
    EntityExists
{
	/**
	 * Get new webhooks
	 *
	 * @param  int $limit
	 * @return array
	 */
	public function getJobs($limit);
}
