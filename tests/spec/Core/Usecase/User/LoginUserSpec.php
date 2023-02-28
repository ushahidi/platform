<?php

namespace spec\Ushahidi\Core\Usecase\User;

use PhpSpec\ObjectBehavior;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Formatter;
use Ushahidi\Contracts\RateLimiter;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Contracts\Repository\ReadRepository;
use Ushahidi\Contracts\PasswordAuthenticator;

class LoginUserSpec extends ObjectBehavior
{
    public function let(
        Authorizer $auth,
        Formatter $format,
        UserRepository $repo,
        PasswordAuthenticator $authenticator,
        RateLimiter $rateLimiter
    ) {
        $repo->beADoubleOf(ReadRepository::class);

        $this->setAuthorizer($auth);
        $this->setFormatter($format);
        $this->setRepository($repo);
        $this->setAuthenticator($authenticator);
        $this->setRateLimiter($rateLimiter);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Ushahidi\Core\Usecase\User\LoginUser');
    }

    public function it_does_interact_with_the_repository_and_authenticator($repo, $authenticator, $format, Entity $user)
    {
        $email = 'test@ushahidi.com';
        $password = 'secret';

        $this->setIdentifiers(compact('email', 'password'));

        $user->getId()->willReturn(1);
        $user->password = 'hash';

        $repo->getByEmail($email)->willReturn($user);

        $authenticator->checkPassword($password, $user->password)->willReturn(true);

        $formatted = ['email' => 'test@ushahidi.com', 'password' => 'hash'];
        $format->__invoke($user)->willReturn($formatted);

        $this->interact()->shouldReturn($formatted);
    }
}
