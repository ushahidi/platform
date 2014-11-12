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

use Ushahidi\Core\Usecase;
use Ushahidi\Core\Data;

use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\ValidatorTrait;

use Ushahidi\Core\Exception\ValidatorException;

class CreateUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		ValidatorTrait;

	/**
	 * @var Ushahidi\Core\Usecase\CreateRepository
	 */
	protected $repo;

	public function __construct(Array $tools)
	{
		$this->setValidator($tools['valid']);
		$this->setAuthorizer($tools['auth']);
		$this->setRepository($tools['repo']);
	}

	/**
	 * @param  Ushahidi\Core\Usecase\CreateRepository $repo
	 * @return void
	 */
	protected function setRepository(CreateRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * Before validation, allow additional binding of input/entity values to
	 * the validator. Can be overloaded as necessary by specific use cases.
	 * @param  Entity $entity
	 * @param  Data   $input
	 * @return void
	 */
	protected function beforeValidate(Data $input)
	{
		// Nothing by default, overloaded uses cases can overload.
	}

	public function interact(Data $input)
	{
		// Apply additional validation bindings.
		$this->beforeValidate($input);

		if (!$this->valid->check($input)) {
			throw new ValidatorException('Failed to validate data for create', $this->valid->errors());
		}

		// Fetch an empty record
		$entity = $this->repo->getEntity();

		$this->verifyCreateAuth($entity, $input);

		$entity = $this->repo->get(
			$this->repo->create($input)
		);

		$this->verifyReadAuth($entity);

		return $entity;
	}
}
