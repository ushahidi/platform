<?php

/**
 * Ushahidi Platform Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Core\Usecase;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\ModifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

class UpdateUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		FormatterTrait,
		ValidatorTrait;

	// - IdentifyRecords for setting entity lookup parameters
	// - ModifyRecords for setting entity modification parameters
	use IdentifyRecords,
		ModifyRecords;

	// - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;

	/**
	 * @var UpdateRepository
	 */
	protected $repo;

	/**
	 * Inject a repository that can update entities.
	 *
	 * @param  UpdateRepository $repo
	 * @return $this
	 */
	public function setRepository(UpdateRepository $repo)
	{
		$this->repo = $repo;
		return $this;
	}

	// Usecase
	public function isWrite()
	{
		return true;
	}

	// Usecase
	public function isSearch()
	{
		return false;
	}

	// Usecase
	public function interact()
	{
		// Fetch the entity with payload applied...
		$entity = $this->getEntity();

		// ... verify that the entity can be updated by the current user
		$this->verifyUpdateAuth($entity);

		// ... verify that the entity is in a valid state
		$this->verifyValid($entity);

		// ... persist the changes
		$this->repo->update($entity);

		// ... verify that the entity can be read by the current user
		$this->verifyReadAuth($entity);

		// ... and return the formatted entity
		return $this->formatter->__invoke($entity);
	}

	/**
	 * Find entity based on identifying parameters, apply the payload.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		// Fetch the entity using the given identifiers
		$entity = $this->repo->get($this->getRequiredIdentifier('id'));

		// ... verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, $this->identifiers);

		// ... and update the entity with the payload
		return $entity->setState($this->payload);
	}
}
