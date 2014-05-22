<?php

namespace spec\Ushahidi\Usecase\User;

use Ushahidi\Entity\User;
use Ushahidi\Tool\Validator;
use Ushahidi\Usecase\User\RegisterRepository;
use Ushahidi\Usecase\User\RegisterData;

use PhpSpec\ObjectBehavior;

class RegisterSpec extends ObjectBehavior
{
	function let(RegisterRepository $repo, Validator $valid)
	{
		$this->beConstructedWith($repo, $valid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\User\Register');
	}

	function it_does_interact_with_the_validator_and_repository($repo, $valid, RegisterData $input)
	{
		$input->email    = 'test@example.com';
		$input->username = 'test';
		$input->password = 'secret';

		$valid->check($input)->shouldBeCalled()->willReturn(true);
		$repo->register($input->email, $input->username, $input->password)->shouldBeCalled()->willReturn(1);

		$this->interact($input);
	}
}
