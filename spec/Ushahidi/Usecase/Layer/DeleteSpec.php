<?php

namespace spec\Ushahidi\Usecase\Layer;

use Ushahidi\Tool\Authorizer;
use Ushahidi\Entity\Layer;
use Ushahidi\Usecase\Layer\DeleteLayerRepository;
use Ushahidi\Usecase\Layer\ReadLayerData;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DeleteSpec extends ObjectBehavior
{
	function let(DeleteLayerRepository $repo, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Layer\Delete');
	}

	function it_fails_when_auth_not_allowed($auth, $repo, ReadLayerData $input, Layer $layer)
	{
		$input->id = 1;
		$layer->id = 1;

		$repo->get($input->id)->willReturn($layer);

		$auth->isAllowed($layer, 'delete')->willReturn(false);
		$auth->getUserId()->willReturn(1);
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_fails_when_the_layer_does_not_exist($auth, $repo, ReadLayerData $input, Layer $layer)
	{
		$input->id = 9999999;
		$layer->id = 0;

		$auth->isAllowed($layer, 'delete')->willReturn(true);

		$repo->get($input->id)->willReturn($layer);

		$this->shouldThrow('Ushahidi\Exception\NotFoundException')->duringInteract($input);
	}

	function it_can_delete_layer_with_valid_input($auth, $repo, ReadLayerData $input, Layer $layer)
	{
		$input->id = 1;
		$layer->id = 1;

		$auth->isAllowed($layer, 'delete')->willReturn(true);

		$repo->get($input->id)->willReturn($layer);

		$repo->deleteLayer($input->id)->willReturn(1);

		$this->interact($input)->shouldReturn($layer);
	}
}
