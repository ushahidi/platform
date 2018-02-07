<?php

/**
 * Ushahidi Platform Entity Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Tos;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreateTos extends CreateUsecase
{

	protected function getEntity()
	{
		$entity = parent::getEntity();

		// Default to the current session user.
		$entity->setState(['user_id' => $this->auth->getUserId()]);

		return $entity;
	}
}
