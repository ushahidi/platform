<?php

/**
 * Ushahidi Authorizer Tool Trait
 *
 * Gives objects:
 * - a method for storing an authorizer instance.
 * - a method for checking auth on an entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Exception\AuthorizerException;

trait AuthorizerTrait
{
	/**
	 * @var Authorizer
	 */
	protected $auth;

	/**
	 * @param  Authorizer $auth
	 * @return void
	 */
	public function setAuthorizer(Authorizer $auth)
	{
		$this->auth = $auth;
		return $this;
	}

	/**
	 * Verifies the current user is allowed $privilege on $entity
	 *
	 * @param  Entity  $entity
	 * @param  String  $privilege
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyAuth(Entity $entity, $privilege)
	{
		if (!$this->auth->isAllowed($entity, $privilege)) {
			throw new AuthorizerException(sprintf(
				'User %d is not allowed to %s resource %s #%d',
				$this->auth->getUserId(),
				$privilege,
				$entity->getResource(),
				$entity->getId()
			));
		}
	}

	/**
	 * Verifies the current user is allowed search access on $entity
	 *
	 * @param  Entity  $entity
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifySearchAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'search');
	}

	/**
	 * Verifies the current user is allowed read access on $entity
	 *
	 * @param  Entity  $entity
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyReadAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'read');
	}

	/**
	 * Verifies the current user is allowed delete access on $entity
	 *
	 * @param  Entity  $entity
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyDeleteAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'delete');
	}

	/**
	 * Verifies the current user is allowed update access on $entity
	 *
	 * @param  Entity  $entity
	 * @param  Data    $input
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyUpdateAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'update');
	}

	/**
	 * Verifies the current user is allowed create access on $entity
	 *
	 * @param  Entity  $entity
	 * @param  Data    $input
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyCreateAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'create');
	}
}
