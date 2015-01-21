<?php

/**
 * Ushahidi Platform Update Message Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\UpdateUsecase;

class UpdateMessage extends UpdateUsecase
{
	// UpdateUsecase
	protected function getEntity()
	{
		// Fetch the entity using the given identifiers
		$entity = $this->repo->get($this->getRequiredIdentifier('id'));

		// ... verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, $this->identifiers);

		if ($entity->direction === $entity::INCOMING) {
			// For incoming messages, users can't actually edit a message, only
			// archived/unarchive and associate with post. Strip everything else.
			$allowed = ['status', 'post_id'];
		} else {
			// For outgoing messages. Update most values, exclude direction and parent id.
			$allowed = [
				'contact_id',
				'data_provider',
				'title',
				'message',
				'datetime',
				'type',
				'status',
			];
		}

		// ... reduce payload to allowed changes
		$payload = array_intersect_key($this->payload, array_flip($allowed));

		// ... and update the entity with the payload
		return $entity->setState($payload);
	}
}
