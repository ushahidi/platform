<?php

/**
 * Ushahidi Platform Update Form Role Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;
use Ushahidi\Core\Entity\FormRole;

class UpdateFormRole extends CreateUsecase
{
	// - VerifyFormLoaded for checking that the form exists
	use VerifyFormLoaded;

	// For form check:
	// - IdentifyRecords
	// - VerifyEntityLoaded
	use IdentifyRecords,
		VerifyEntityLoaded;

	/**
	 * Get an empty entity.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		return $this->repo->getEntity();
	}

	// Usecase
	public function interact()
	{
		// First verify that the form even exists
		$this->verifyFormExists();

		// Fetch a default entity and ...
		$entity = $this->getEntity();

		// ... verify the current user has have permissions
		$this->verifyCreateAuth($entity);


		// Get each item in the collection
		$entities = [];
		$form_id = $this->getRequiredIdentifier('form_id');
		foreach ($this->getPayload('roles') as $role_id) {
			// .. generate an entity for the item
			$entity = $this->repo->getEntity(compact('role_id', 'form_id'));
			// ... verify that the entity is in a valid state
			$this->verifyValid($entity);
			// ... and save it for later
			$entities[] = $entity;
		}

		// ... persist the new collection
		$this->repo->updateCollection($entities);

		// ... and finally format it for output
		return $this->formatter->__invoke($entities);
	}
}
