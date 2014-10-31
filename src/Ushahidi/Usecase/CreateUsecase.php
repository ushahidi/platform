<?php

/**
 * Ushahidi Platform Entity Create Use Case
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

use Ushahidi\Exception\ValidatorException;

class CreateUsecase implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		ValidatorTrait;

	/**
	 * @var Ushahidi\Usecase\CreateRepository
	 */
	protected $repo;

	public function __construct(Array $tools)
	{
		$this->setValidator($tools['valid']);
		$this->setAuthorizer($tools['auth']);
		$this->setRepository($tools['repo']);
	}

	/**
	 * @param  Ushahidi\Usecase\CreateRepository $repo
	 * @return void
	 */
	private function setRepository(CreateRepository $repo)
	{
		$this->repo = $repo;
	}

	public function interact(Data $input)
	{
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
