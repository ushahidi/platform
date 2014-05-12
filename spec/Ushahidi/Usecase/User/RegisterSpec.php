<?php

namespace spec\Ushahidi\Usecase\User;

use Ushahidi\Entity\User;
use Ushahidi\Entity\UserRepository;
use Ushahidi\Tool\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RegisterSpec extends ObjectBehavior
{
	function let(UserRepository $repo, Validator $valid, User $user)
	{
		$user->beConstructedWith([]);
		$user->email = 'test@example.com';
		$user->username = 'test';
		$user->password = 'secret';

		$this->beConstructedWith($repo, $valid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\User\Register');
	}

	function it_does_interact_with_the_validator_and_repository($repo, $valid, $user)
	{
		$valid->check($user)->shouldBeCalled()->willReturn(true);
		$repo->add($user)->shouldBeCalled()->willReturn(1);

		$this->interact($user);
	}
}
