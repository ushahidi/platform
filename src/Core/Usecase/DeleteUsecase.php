<?php

/**
 * Ushahidi Platform Delete Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Core\Data;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\DeleteData;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Traits\VerifyEntityLoaded;
use Ushahidi\Core\Exception\AuthorizerException;

class DeleteUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait;

	// - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;

	// Ushahidi\Core\Usecase\DeleteRepository
	protected $repo;

	public function __construct(Array $tools)
	{
		$this->setRepository($tools['repo']);
		$this->setAuthorizer($tools['auth']);
	}

	protected function setRepository(DeleteRepository $repo)
	{
		$this->repo = $repo;
	}

	public function interact(Data $input)
	{
		$entity = $this->getEntity($input);

		$this->verifyEntityLoaded($entity, $input->id);

		$this->verifyDeleteAuth($entity);

		$this->repo->delete($input->id);

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
