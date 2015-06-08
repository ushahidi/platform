<?php

/**
 * Ushahidi Platform Get Set for Set/Post Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

use Ushahidi\Core\Exception\NotFoundException;

trait GetSet
{
	/**
	 * Find entity based on identifying parameters.
	 *
	 * @return Entity
	 */
	protected function getSetEntity()
	{
		// Entity will be loaded using the provided id
		$id = $this->getRequiredIdentifier('set_id');

		// ... attempt to load the entity
		$entity = $this->getSetRepository()->get($id);

		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('id'));

		// ... then return it
		return $entity;
	}

	// VerifyEntityLoaded
	abstract protected function verifyEntityLoaded(Entity $entity, $lookup);

	// IdentifyRecords
	abstract protected function getRequiredIdentifier($name);

	// SetRepositoryTrait
	abstract public function getSetRepository();
}
