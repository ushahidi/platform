<?php

/**
 * Repository for Post Data Export jobs
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;

interface PostDataExportJobRepository extends
    EntityGet,
    EntityExists
{
	/**
	 * Get new Post Data Exports
	 *
	 * @param  int $limit
	 * @return array
	 */
	public function getJobs($limit);
}
