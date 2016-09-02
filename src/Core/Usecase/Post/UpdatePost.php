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

class UpdatePost extends UpdateUsecase
{
	// This replaces the default getEntity() logic to allow loading
	// posts by locale, parent id and id.
	use FindPostEntity {
		// In the case of updates, we have to apply the payload after fetch.
		getEntity as private getEntityWithoutPayload;
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
