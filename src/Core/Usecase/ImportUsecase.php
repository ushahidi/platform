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
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Tool\Transformer;
use Ushahidi\Core\Traits\ModifyRecords;

class ImportUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		FormatterTrait,
		ValidatorTrait;

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
	public function setRepository(ImportRepository $repo)
	{
		$this->repo = $repo;
		return $this;
	}

	/**
	 * @var Traversable
	 */
	protected $payload;

	/**
	 * Inject a repository that can create entities.
	 *
	 * @todo  setPayload doesn't match signature for other usecases
	 *
	 * @param  $repo Iterator
	 * @return $this
	 */
	public function setPayload(Traversable $payload)
	{
		$this->payload = $payload;
		return $this;
	}

	/**
	 * @var Transformer
	 */
	protected $transformer;

	/**
	 * Inject a repository that can create entities.
	 *
	 * @param  $repo Iterator
	 * @return $this
	 */
	public function setTransformer(Transformer $transformer)
	{
		$this->transformer = $transformer;
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
		// Start count of records processed, and errors
		$processed = $errors = 0;

		// Fetch an empty entity..
		$entity = $this->getEntity();

		// ... verify that the entity can be created by the current user
		$this->verifyImportAuth($entity);

		// Fetch a record
		foreach ($this->payload as $index => $record) {

			// ... transform record
			$entity = $this->transform($record);

			// ... verify that the entity can be created by the current user
			$this->verifyCreateAuth($entity);

			// ... verify that the entity is in a valid state
			$this->verifyValid($entity);

			// ... persist the new entity
			$id = $this->repo->create($entity);

			$processed++;
		}

		// ... and return the formatted entity
		return [
			'processed' => $processed,
			'errors' => $errors
		];
	}

	// ValidatorTrait
	protected function verifyValid(Entity $entity)
	{
		if (!$this->validator->check($entity->asArray())) {
			$this->validatorError($entity);
		}
	}

	/**
	 * Get an empty entity
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		return $this->repo->getEntity();
	}

	/**
	 * [transform description]
	 * @return [type] [description]
	 */
	protected function transform($record)
	{
		$record = $this->transformer->interact($record);

		return $this->repo->getEntity()->setState($record);
	}
}
