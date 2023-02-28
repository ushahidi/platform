<?php

namespace App\Passport;

use Laravel\Passport\Bridge\User;
use Ushahidi\Core\Tool\Authenticator\Password;
use Ushahidi\Core\Usecase\User\LoginUser;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Ushahidi\Core\Entity\UserRepository as EntityUserRepository;

class UserRepository implements UserRepositoryInterface
{
    protected $usecase;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(LoginUser $usecase, Password $passwordAuth, EntityUserRepository $userRepo)
    {
        $this->usecase = $usecase;

        $this->usecase->setAuthenticator($passwordAuth);

        $this->usecase->setRepository($userRepo);

        // $this->usecase->setRateLimiter($rateLimiter);
    }

    /**
     * @inheritdoc
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $this->usecase
            ->setIdentifiers([
                'email' => $username,
                'password' => $password,
            ]);

        try {
            $data = $this->usecase->interact();

            return new User($data['id']);
        } catch (\Exception $e) {
            return false;
        }
    }
}
