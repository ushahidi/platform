<?php

/**
 * Ushahidi Platform Entity Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\BulkUsecase;

class DeletePostBulk extends BulkUsecase
{
	/**
	 * Execute actions against the result set
	 *
	 * @return null
	 */
	protected function executeActions($records, $actions = [])
	{
		return $this->repo->bulkDelete($records);
	}

	/**
	 * Get actions for delete use case
	 *
	 * @return null
	 */
	protected function getActions()
	{
		return ["deleted" => true];
	}	
}