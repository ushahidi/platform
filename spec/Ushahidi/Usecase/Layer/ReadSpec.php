<?php

namespace spec\Ushahidi\Usecase\Layer;

use Ushahidi\Usecase\Layer\ReadLayerData;
use Ushahidi\Usecase\Layer\ReadLayerRepository;

use Ushahidi\Entity\Layer;
use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReadSpec extends ObjectBehavior
{
	function let(ReadLayerRepository $repo, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Layer\Read');
	}

	function it_fails_when_the_layer_does_not_exist($repo, ReadLayerData $input, Layer $layer)
	{
		$input->id = 9999999;
		$layer->id = 0; // an empty id is invalid

		$repo->get($input->id)->willReturn($layer);

		$this->shouldThrow('Ushahidi\Exception\NotFoundException')->duringInteract($input);
	}

	function it_fails_when_not_allowed($repo, $auth, ReadLayerData $input, Layer $layer)
	{
		$input->id = 1;
		$layer->id = 1;

		$repo->get($input->id)->willReturn($layer);
		$auth->isAllowed($layer, 'get')->willReturn(false);

		// the exception will ask for the user id
		$auth->getUserId()->shouldBeCalled();

		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_can_read_a_layer($repo, $auth, ReadLayerData $input, Layer $layer)
	{
		$input->id = 1;
		$layer->id = 1;

		$repo->get($input->id)->willReturn($layer);
		$auth->isAllowed($layer, 'get')->willReturn(true);

		$this->interact($input)->shouldReturn($layer);
	}
}
