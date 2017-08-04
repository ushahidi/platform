<?php

/**
 * Ushahidi Platform Entity Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Traversable;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

class LockUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		FormatterTrait;

	// - IdentifyRecords for setting entity lookup parameters
	use IdentifyRecords;

	// - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;


	/**
	 * @var ImportRepository
	 */
	protected $repo;

	/**
	 * Inject a repository that can create entities.
	 *
	 * @param  $repo ImportRepository
	 * @return $this
	 */
	public function setRepository(LockRepository $repo)
	{
		$this->repo = $repo;
		return $this;
	}

	// Usecase
	public function isWrite()
	{
		return false;
	}

	// Usecase
	public function isSearch()
	{
		return false;
	}

	// Usecase
	public function interact()
	{
		// Start count of records processed, and errors
		$processed = $errors = 0;

		// Fetch an empty entity..
		$entity = $this->getEntity();

		// ... verify that the entity can be created by the current user
		$this->verifyImportAuth($entity);

	 	$created_entities = array();
		// Fetch a record
		foreach ($this->payload as $index => $record) {
			// ... transform record
			$entity = $this->transform($record);

			// Ensure that under review is correctly mapped to draft
			if (strcasecmp($entity->status, 'under review')== 0) {
				$entity->setState(['status' => 'draft']);
			}
			// ... verify that the entity can be created by the current user
			$this->verifyCreateAuth($entity);

			// ... persist the new entity
			$id = $this->repo->create($entity);
			$created_entities[] = $id;
			$processed++;
		}

		// ... and return the formatted entity
		return [
			'created_ids' => $created_entities,
			'processed' => $processed,
			'errors' => $errors
		];
	}

	

	/**
	 * Find entity based on identifying parameters.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		// Entity will be loaded using the provided id
		$id = $this->getRequiredIdentifier('post_id');

		// ... attempt to load the entity
		$entity = $this->repo->get($id);

		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('id'));

		// ... then return it
		return $entity;
	}

}
