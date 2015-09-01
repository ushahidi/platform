<?php

/**
 * Ushahidi Platform Update Notification Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Notification;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\UpdateUsecase;

class UpdateNotification extends UpdateUsecase
{
	public function interact()
	{
		// Fetch the entity and apply the payload...
		$entity = $this->getEntity()->setState($this->payload);

		// ... verify that the entity can be updated by the current user
		$this->verifyUpdateAuth($entity);

		// ... verify that the entity is in a valid state
		$this->verifyValid($entity);

		// ... persist the changes
		$this->repo->update($entity);

		// ... check that the entity can be read by the current user
		if ($this->auth->isAllowed($entity, 'read') and
			// ... check that the subscription status changed
			$entity->hasChanged('is_subscribed')) {
			// ... and either load the updated entity from the storage layer
			$updated_entity = $this->getEntity();
			// ... and return the updated, formatted entity
			return $this->formatter->__invoke($updated_entity);
		} else {
			// ... or just return nothing
			return;
		}
	}
}
