<?php

/**
 * Ushahidi Platform Create Form Attribute Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Usecase\Contact\CreateContact;
use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;

class CreateFormContact extends CreateContact
{
	// - VerifyStageLoaded for checking that the stage exists
	use VerifyFormLoaded;

	// For form check:
	// - IdentifyRecords
	use VerifyEntityLoaded;
	use IdentifyRecords;
	//	VerifyEntityLoaded;

	// CreateUsecase
	protected function getEntity()
	{
		$entity = parent::getEntity();

		// $this->verifyStageExists($entity);

		return $entity;
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
		//$form_id = $this->getRequiredIdentifier('form_id');
		$countryCode = $this->getPayload('country_code');
		foreach (explode(',', $this->getPayload('contacts')) as $contact) {
			// .. generate an entity for the item
			$entity = $this->repo->getEntity(compact('contact'));
			$entity->country_code = $countryCode;
			$entity->setState(
				[
					'created' => time(),
					'can_notify' => true,
					'type' => 'phone',
					'contact' => $entity->contact,
				]
			);
			// ... verify that the entity is in a valid state
			$this->verifyValid($entity);
			// ... and save it for later
			$entities[] = $entity;
		}

		// ... persist the new collection
		$this->repo->updateCollection($entities, $this->getPayload('form_id'));

		// ... and finally format it for output
		return $this->formatter->__invoke($entities);
	}
}
