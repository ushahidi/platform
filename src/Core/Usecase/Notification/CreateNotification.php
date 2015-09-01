<?php

/**
 * Add notification notification
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Notification;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreateNotification extends CreateUsecase
{
	public function interact()
	{
		// Fetch a default entity and apply the payload...
		$entity = $this->getEntity();

		// ... verify that the entity can be created by the current user
		$this->verifyCreateAuth($entity);

		// ... verify that the entity is in a valid state
		$this->verifyValid($entity);

		// ... persist the new entity
		$id = $this->repo->create($entity);

		// ... get the newly created entity
		$entity = $this->getCreatedEntity($id);

		// ... check that the entity can be read by the current user
		if ($this->auth->isAllowed($entity, 'read') and
			// ... check that the entity was persisted
			!is_null($entity->id)) {
			// ... and either return the formatted entity
			return $this->formatter->__invoke($entity);
		} else {
			// ... or just return nothing
			return;
		}
	}
}