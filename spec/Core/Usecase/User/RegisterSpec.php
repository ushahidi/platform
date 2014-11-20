<?php

namespace spec\Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\User\RegisterRepository;
use Ushahidi\Core\Usecase\User\RegisterData;

use PhpSpec\ObjectBehavior;

class RegisterSpec extends ObjectBehavior
{
	function let(RegisterRepository $repo, Validator $valid)
	{
		$this->beConstructedWith($repo, $valid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Usecase\User\Register');
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
