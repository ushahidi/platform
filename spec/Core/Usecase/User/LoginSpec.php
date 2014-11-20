<?php

namespace spec\Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Tool\PasswordAuthenticator as Authenticator;
use Ushahidi\Core\Usecase\User\LoginData;

use PhpSpec\ObjectBehavior;

class LoginSpec extends ObjectBehavior
{
	function let(UserRepository $repo, Validator $valid, Authenticator $auth)
	{
		$this->beConstructedWith($repo, $valid, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Usecase\User\Login');
	}

	function it_does_interact_with_the_validator_and_repository_and_authenticator($valid, $repo, $auth, LoginData $input)
	{
		$input->username = 'test';
		$input->password = 'secret';

		$valid->check($input)->shouldBeCalled()->willReturn(true);
		$repo->getByUsername('test')->shouldBeCalled()->willReturn((object) ['id' => 1, 'password' => 'hash']);
		$auth->checkPassword('secret', 'hash')->shouldBeCalled()->willReturn(true);
		$this->interact($input)->shouldReturn(1);
	}
}
