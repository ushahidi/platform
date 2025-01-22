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

namespace Ushahidi\Core\Usecase\Concerns;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer as AuthorizerInterface;
use Ushahidi\Core\Exception\AuthorizerException;

trait Authorizer
{
    /**
     * @var AuthorizerInterface
     */
    protected $auth;

    /**
     * @param  AuthorizerInterface $auth
     * @return self
     */
    public function setAuthorizer(AuthorizerInterface $auth)
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * Verifies the current user is allowed $privilege on $entity
     *
     * @param  Entity  $entity
     * @param  string  $privilege
     *
     * @return void
     *
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
     * Verifies the current user is allowed lock access on $entity
     *
     * @param  Entity  $entity
     * @return void
     * @throws AuthorizerException
     */
    protected function verifyLockAuth(Entity $entity)
    {
        $this->verifyAuth($entity, 'lock');
    }

    /**
     * Verifies the current user is allowed delete access on $entity
     *
     * @param  Entity  $entity
     *
     * @return void
     *
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
     *
     * @return void
     *
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
     *
     * @return void
     *
     * @throws AuthorizerException
     */
    protected function verifyCreateAuth(Entity $entity)
    {
        $this->verifyAuth($entity, 'create');
    }

    /**
     * Verifies the current user is allowed import access on $entity
     *
     * @param  Entity  $entity
     *
     * @return void
     *
     * @throws AuthorizerException
     */
    protected function verifyImportAuth(Entity $entity)
    {
        $this->verifyAuth($entity, 'import');
    }

    /**
     * Get all allowed privs on an Entity
     * @param  Entity $entity
     * @return array
     */
    protected function getAllowedPrivs(Entity $entity)
    {
        return $this->auth->getAllowedPrivs($entity);
    }
}
