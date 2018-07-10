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
use Ushahidi\Core\Tool\RateLimiter;

class RegisterUser extends CreateUsecase
{
    /**
     * @var RateLimiter
     */
    protected $rateLimiter;

    /**
     * @param RateLimiter $rateLimiter
     */
    public function setRateLimiter(RateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function interact()
    {
        // fetch entity
        $entity = $this->getEntity();

        // Rate limit registration attempts
        $this->rateLimiter->limit($entity);

        // verify that registration can be done in this case
        $this->verifyRegisterAuth($entity);

        // verify that the entity is in a valid state
        $this->verifyValid($entity);

        // persist the new entity
        $id = $this->repo->register($entity);

        // get the newly created entity
        $entity = $this->getCreatedEntity($id);

        // return the formatted entity
        return $this->formatter->__invoke($entity);
    }

    protected function verifyRegisterAuth(Entity $entity)
    {
        $this->verifyAuth($entity, 'register');
    }
}
