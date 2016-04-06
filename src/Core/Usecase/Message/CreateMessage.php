<?php

/**
 * Create Message Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreateMessage extends CreateUsecase
{

	protected function getEntity()
	{
		$entity = parent::getEntity();

		// Retrieve message type and data provider
		// from incoming message when replying to a message
		if (! empty($this->payload['parent_id'])) {
			$parent = $this->repo->get($this->payload['parent_id']);
			$entity->setState(['type' => $parent->type,
							   'data_provider' => $parent->data_provider]);
		}

		// If no user information is provided, default to the current session user.
		if (
			empty($entity->user_id) &&
			$this->auth->getUserId()
		) {
			$entity->setState(['user_id' => $this->auth->getUserId()]);
		}

		return $entity;
	}
}
