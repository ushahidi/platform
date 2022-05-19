<?php

/**
 * Ushahidi Platform User Get Reset Token Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Contracts\Mailer;
use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Concerns\Translator as TranslatorTrait;
use Ushahidi\Core\Concerns\ModifyRecords;
use Ushahidi\Contracts\Repository\Usecase\UserResetPasswordRepository;

class GetResetToken implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        TranslatorTrait;

    // - ModifyRecords for setting entity modification parameters
    use ModifyRecords;

    // Usecase
    public function isWrite()
    {
        return false;
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
     * @param  $repo UserResetPasswordRepository
     * @return $this
     */
    public function setRepository(UserResetPasswordRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * Inject a mailer
     *
     * @param  $mailer Mailer
     * @return $this
     */
    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    public function interact()
    {
        // Fetch user by email
        $entity = $this->getEntity();

        if ($entity->getId()) {
            // Generate a reset token
            $token = $this->repo->getResetToken($entity);

            // Email the reset token
            $this->mailer->send(
                $entity->email,
                'resetpassword',
                [
                    'token' => $token
                ]
            );
        }

        // Return an empty success response regardless
        // if the user was found or not
        return;
    }

    protected function getEntity()
    {
        // Entity will be loaded using the provided email
        $email = $this->getPayload('email');

        // ... attempt to load the user by email
        $entity = $this->repo->getByEmail($email);

        // ... then return it
        return $entity;
    }
}
