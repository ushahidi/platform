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

use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Usecase\Contact\CreateContact;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;

class CreateFormContact extends CreateContact
{
	use VerifyFormLoaded;

	// For form check:
	use VerifyEntityLoaded;
	use IdentifyRecords;

	protected function getEntity()
	{
		$entity = parent::getEntity();

		// Add user id if this is not provided
		if (empty($entity->user_id) && $this->auth->getUserId()) {
			$entity->setState(['user_id' => $this->auth->getUserId()]);
		}

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
		$invalid = [];
		$countryCode = $this->getPayload('country_code');
		$contacts = explode(',', $this->getPayload('contacts'));
		foreach ($contacts as $contact) {
			// .. generate an entity for the item
			$entity = $this->repo->getEntity(compact('contact'));
			/**
			 * we only use this field for validation
			 * we check that country code + phone number are valid.
			 * country_code is unset before saving the entity
			 */
			$entity->country_code = $countryCode;
			$entity->setState(
				[
					'created' => time(),
					'can_notify' => true,
					'type' => 'phone',
					'contact' => $entity->contact,
				]
			);
			// ... and save it for later
			$entities[] = $entity;

			if (!$this->validator->check($entity->asArray())) {
				$invalid[$entity->contact] = $this->validator->errors();
			}
		}
		// FIXME: move to collection error trait?
		if (!empty($invalid)) {
			$invalidList = implode(',', array_keys($invalid));
			throw new ValidatorException(sprintf(
				'The following contacts are invalid:',
				$invalidList
			), $invalid);
		} else {
			// ... persist the new collection
			$this->repo->updateCollection($entities, $this->getPayload('form_id'));
			// ... and finally format it for output
			return $this->formatter->__invoke($entities);
		}
	}
}
