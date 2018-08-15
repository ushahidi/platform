<?php

/**
 * Ushahidi Platform Reset User Password Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Usecase;
use Ushahidi\Core\Tool\Mailer;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\ModifyRecords;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Tool\ValidatorTrait;

class ResetUserPassword implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        ValidatorTrait;

    // - ModifyRecords for setting search parameters
    use ModifyRecords;

    // Usecase
    public function isWrite()
    {
        return true;
    }

    // Usecase
    public function isSearch()
    {
        return false;
    }

    /**
     * @var ResetPasswordRepository
     */
    protected $repo;

    /**
     * Inject a repository
     *
     * @param  $repo ResetPasswordRepository
     * @return $this
     */
    public function setRepository(ResetPasswordRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    public function interact()
    {
        $token = $this->getPayload('token');
        $password = $this->getPayload('password');
        $entity_array = [
            'token' => $token,
            'password' => $password
        ];

        $this->verifyValid($entity_array);
            
        $this->repo->setPassword($token, $password);

        // And delete the token
        $this->repo->deleteResetToken($token);

        return;
    }

    // ValidatorTrait
    protected function verifyValid(array $entity_array)
    {
        if (!$this->validator->check($entity_array)) {
            $entity = $this->repo->getEntity();
            $this->validatorError($entity);
        }
    }
}
