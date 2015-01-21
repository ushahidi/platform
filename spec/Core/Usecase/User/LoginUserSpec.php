<?php

namespace spec\Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Tool\PasswordAuthenticator;

use PhpSpec\ObjectBehavior;

class LoginUserSpec extends ObjectBehavior
{
	function let(Authorizer $auth, Formatter $format, UserRepository $repo, PasswordAuthenticator $authenticator)
	{
		$repo->beADoubleOf('Ushahidi\Core\Usecase\ReadRepository');

		$this->setAuthorizer($auth);
		$this->setFormatter($format);
		$this->setRepository($repo);
		$this->setAuthenticator($authenticator);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Usecase\User\LoginUser');
	}

	function it_does_interact_with_the_repository_and_authenticator($repo, $authenticator, $format, Entity $user)
	{
		$username = 'test';
		$password = 'secret';

		$this->setIdentifiers(compact('username', 'password'));

		$user->getId()->willReturn(1);
		$user->password = 'hash';

		$repo->getByUsername($username)->willReturn($user);

		$authenticator->checkPassword($password, $user->password)->willReturn(true);

		$formatted = ['username' => 'test', 'password' => 'hash'];
		$format->__invoke($user)->willReturn($formatted);

		$this->interact()->shouldReturn($formatted);
	}
}
