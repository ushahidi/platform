<?php

/**
 * Ushahidi Platform Entity Post Data Export Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\PostDataExport;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreatePostDataExport extends CreateUsecase
{
	protected function getEntity()
	{
		$entity = parent::getEntity();

		// Add user id if this is not provided
		if (empty($entity->user_id) && $this->auth->getUserId()) {
			$entity->setState(['user_id' => $this->auth->getUserId()]);
		}

		return $entity;
	}
}
