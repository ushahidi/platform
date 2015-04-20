<?php

/**
 * Ushahidi Platform User Login Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\PasswordAuthenticator;
use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Exception\AuthorizerException;
use Ushahidi\Core\Entity;

class RegisterUser extends CreateUsecase
{
	public function interact()
	{
		// fetch entity
		$entity = $this->getEntity();

		// verify that registration can be done in this case
		$this->verifyRegisterAuth($entity);

		// verify that the entity is in a valid state
		$this->verifyValid($entity);

		// persist the new entity
		$id = $this->repo->register($entity);

		// get the newly created entity
		$entity = $this->getCreatedEntity($id);

		// verify that the entity can be read by the current user
		$this->verifyReadAuth($entity);

		// return the formatted entity
		return $this->formatter->__invoke($entity);
	}

	protected function verifyRegisterAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'register');
	}
}
