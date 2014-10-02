<?php

namespace spec\Ushahidi\Usecase\Layer;

use Ushahidi\Usecase\Layer\SearchLayerData;
use Ushahidi\Usecase\Layer\SearchLayerRepository;

use Ushahidi\Entity\Layer;
use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SearchSpec extends ObjectBehavior
{
	function let(SearchLayerRepository $repo, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Layer\Search');
	}

	function it_can_search_for_layers($repo, $auth, SearchLayerData $input, Layer $layer)
	{
		$layers = [$layer];

		$repo->search($input)->willReturn($layers);
		$auth->isAllowed($layer, 'get')->willReturn(true);

		$this->interact($input)->shouldReturn($layers);
	}

	function it_removes_disallowed_layers($repo, $auth, SearchLayerData $input, Layer $layer, Layer $disallowed)
	{
		$layers = [$layer, $disallowed];
		$allowed = [$layer];

		$repo->search($input)->willReturn($layers);
		$auth->isAllowed($layer, 'get')->willReturn(true);
		$auth->isAllowed($disallowed, 'get')->willReturn(false);

		$this->interact($input)->shouldReturn($allowed);
	}
}
