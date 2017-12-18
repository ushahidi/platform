<?php

/**
 * Ushahidi Platform Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\UpdateUsecase;

class WebhookUpdatePost extends UpdateUsecase
{
	// This replaces the default getEntity() logic to allow loading
	// posts by locale, parent id and id.
	use FindPostEntity {
		// In the case of updates, we have to apply the payload after fetch.
		getEntity as private getEntityWithoutPayload;
	}

	// Usecase
	public function interact()
	{
		// Fetch the entity and apply the payload...
		$entity = $this->getEntity()->setState($this->payload);

		// ... verify that the entity is in a valid state
		$this->verifyValid($entity);

		// ... persist the changes
		$this->repo->updateFromService($entity);

		// ... check that the entity can be read by the current user
		if ($this->auth->isAllowed($entity, 'read')) {
			// ... and either load the updated entity from the storage layer
			$updated_entity = $this->getEntity();

			// ... and return the updated, formatted entity
			return $this->formatter->__invoke($updated_entity);
		} else {
			// ... or just return nothing
			return;
		}
	}

	// UpdateUsecase
	protected function getEntity()
	{
		return $this->getEntityWithoutPayload();
	}

	// UpdateUsecase
	protected function verifyValid(Entity $entity)
	{
		$changed = $entity->getChanged();

		// Always pass values to validation

		if (isset($entity->values)) {
			$changed['values'] = $entity->values;
		}

		if (!$this->validator->check($changed, $entity->asArray())) {
			$this->validatorError($entity);
		}
	}
}
