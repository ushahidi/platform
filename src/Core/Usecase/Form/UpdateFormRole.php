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
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;
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

	// CreateUsecase
	protected function getEntity()
	{
		return parent::getEntity()->setState([
			'form_id' => $this->getRequiredIdentifier('form_id'),
			'roles' => $this->getPayload('roles'),
		]);
	}
	
	// Usecase
	public function interact()
	{
		// Fetch a default entity and apply the payload...
		$entity = $this->getEntity();

		// ... verify that the entity can be created by the current user
		$this->verifyCreateAuth($entity);

		// ... verify that the entity is in a valid state
		$this->verifyFormExists($entity);

		// ... verify that the entity is in a valid state
		$this->verifyValid($entity);

		// ... persist the new entity
		$form_roles = $this->repo->update($entity);
		
		$results = [];
		foreach ($form_roles as $form_role) {
			$entity->setState($form_role);
			$results[] = $this->formatter->__invoke($entity);
		}

		$output = [
			'count'   => count($results),
			'results' => $results,
		];

		return $output;

	}
	
}
