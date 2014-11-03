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
use Ushahidi\Core\Data;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Traits\VerifyEntityLoaded;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Exception\AuthorizerException;

class UpdateUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		ValidatorTrait;

	// - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;

	// Ushahidi\Core\Usecase\CreateRepository
	protected $repo;

	private $updated = [];

	public function __construct(Array $tools)
	{
		$this->setValidator($tools['valid']);
		$this->setAuthorizer($tools['auth']);
		$this->setRepository($tools['repo']);
	}

	private function setRepository(UpdateRepository $repo)
	{
		$this->repo = $repo;
	}

	public function interact(Data $input)
	{
		$entity = $this->getEntity($input);

		$this->verifyEntityLoaded($entity, $input->id);

		$this->verifyUpdateAuth($entity, $input);

		// We only want to work with values that have been changed
		$update = $input->getDifferent($entity->asArray());

		if (!$this->valid->check($update)) {
			throw new ValidatorException('Failed to validate data for update', $this->valid->errors());
		}

		// Determine what changes to were made
		$this->updated = $update->asArray();

		$this->repo->update($entity->id, $update);

		// Reload the entity to get the most recent data
		$entity = $this->repo->get($entity->id);

		$this->verifyReadAuth($entity);

		return $entity;
	}

	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * Find entity based on read data
	 * @param  Data    $input
	 * @return Entity
	 */
	protected function getEntity(Data $input)
	{
		return $this->repo->get($input->id);
	}
}
