<?php

namespace spec\Ushahidi\Usecase\Tag;

use Ushahidi\Usecase\Tag\SearchTagData;
use Ushahidi\Usecase\Tag\SearchTagRepository;

use Ushahidi\Entity\Tag;
use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SearchSpec extends ObjectBehavior
{
	function let(SearchTagRepository $repo, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Tag\Search');
	}

	function it_can_search_for_tags($repo, $auth, SearchTagData $input, Tag $tag)
	{
		$tags = [$tag];

		$repo->search($input)->willReturn($tags);
		$auth->isAllowed($tag, 'get')->willReturn(true);

		$this->interact($input)->shouldReturn($tags);
	}

	function it_removes_disallowed_tags($repo, $auth, SearchTagData $input, Tag $tag, Tag $disallowed)
	{
		$tags = [$tag, $disallowed];
		$allowed = [$tag];

		$repo->search($input)->willReturn($tags);
		$auth->isAllowed($tag, 'get')->willReturn(true);
		$auth->isAllowed($disallowed, 'get')->willReturn(false);

		$this->interact($input)->shouldReturn($allowed);
	}
}
