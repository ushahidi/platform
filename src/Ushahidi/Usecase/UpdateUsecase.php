<?php

/**
 * Ushahidi Platform Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase;

use Ushahidi\Usecase;
use Ushahidi\Data;
use Ushahidi\Tool\AuthorizerTrait;
use Ushahidi\Tool\ValidatorTrait;
use Ushahidi\Traits\VerifyEntityLoaded;
use Ushahidi\Exception\ValidatorException;
use Ushahidi\Exception\AuthorizerException;

class UpdateUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		ValidatorTrait;

	// - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;

	// Ushahidi\Usecase\CreateRepository
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
		$entity = $this->repo->get($input->id);

		$this->verifyEntityLoaded($entity, $input->id);

		if (!$this->auth->isAllowed($entity, 'update')) {
			throw new AuthorizerException(sprintf(
				'User %d is not allowed to update resource %s #%d',
				$this->auth->getUserId(),
				$entity->getResource(),
				$entity->id
			));
		}

		// We only want to work with values that have been changed
		$update = $input->getDifferent($entity->asArray());

		if (!$this->valid->check($update)) {
			throw new ValidatorException('Failed to validate data for update', $this->valid->errors());
		}

		// Determine what changes to were made
		$this->updated = $update->asArray();

		$this->repo->update($entity->id, $update);

		// Reload the entity to get the most recent data
		$entity = $this->repo->get($input->id);

		if (!$this->auth->isAllowed($entity, 'read')) {
			throw new AuthorizerException(sprintf(
				'User %d is not allowed to read resource %s #%d',
				$this->auth->getUserId(),
				$entity->getResource(),
				$entity->id
			));
		}

		return $entity;
	}

	public function getUpdated()
	{
		return $this->updated;
	}
}
