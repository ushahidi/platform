<?php

/**
 * Ushahidi Platform Read Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase;

use Ushahidi\Data;
use Ushahidi\Usecase;
use Ushahidi\ReadData;
use Ushahidi\Tool\AuthorizerTrait;
use Ushahidi\Traits\VerifyEntityLoaded;

use Ushahidi\Exception\AuthorizerException;

class ReadUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait;

	// - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;

	// Ushahidi\Usecase\ReadRepository
	protected $repo;

	public function __construct(Array $tools)
	{
		$this->setRepository($tools['repo']);
		$this->setAuthorizer($tools['auth']);
	}

	private function setRepository(ReadRepository $repo)
	{
		$this->repo = $repo;
	}

	public function interact(Data $input)
	{
		$entity = $this->getEntity($input);

		$this->verifyEntityLoaded($entity, $input->id);

		if (!$this->auth->isAllowed($entity, 'read')) {
			throw new AuthorizerException(sprintf(
				'User %d is not allowed to view resource %s: %d',
				$this->auth->getUserId(),
				$entity->getResource(),
				$entity->id
			));
		}

		return $entity;
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
