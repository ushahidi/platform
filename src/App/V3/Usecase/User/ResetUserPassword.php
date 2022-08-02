<?php

/**
 * Ushahidi Platform Reset User Password Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Usecase\User;

use Ushahidi\Contracts\Usecase;
use Ushahidi\App\V3\Usecase\Concerns\ModifyRecords;
use Ushahidi\App\V3\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\App\V3\Usecase\Concerns\Validator as ValidatorTrait;
use Ushahidi\App\V3\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\App\V3\Usecase\Concerns\Translator as TranslatorTrait;
use Ushahidi\Contracts\Repository\Usecase\UserResetPasswordRepository;

class ResetUserPassword implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides
    // a setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        ValidatorTrait,
        TranslatorTrait;

    // ModifyRecords for setting search parameters
    use ModifyRecords;

    protected $repo;

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

    public function setRepository(UserResetPasswordRepository $repo)
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

        return [];
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
