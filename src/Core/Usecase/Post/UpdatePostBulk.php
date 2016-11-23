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

class UpdatePostBulk extends BulkUsecase
{
	/**
	 * Execute actions against the result set
	 *
	 * @return null
	 */
	protected function executeActions($records, $actions = [])
	{
		$actions = $this->getActions();
		return $this->repo->bulkUpdate($records, $actions);
	}

	/**
	 * Execute execute validations against the result set
	 *
	 * @return null
	 */
	protected function validateRecords($entity, $records)
	{
		$actions = $this->getActions();
		$status = $actions['status'];
		if (!$this->validator->validateRecords($status, $records))
		{
			$this->validatorError($entity);
		}
	}
}