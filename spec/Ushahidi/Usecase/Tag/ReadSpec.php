<?php

namespace spec\Ushahidi\Usecase\Tag;

use Ushahidi\Usecase\Tag\ReadTagData;
use Ushahidi\Usecase\Tag\ReadTagRepository;

use Ushahidi\Entity\Tag;
use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReadSpec extends ObjectBehavior
{
	function let(ReadTagRepository $repo, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Tag\Read');
	}

	function it_fails_when_the_tag_does_not_exist($repo, ReadTagData $input, Tag $tag)
	{
		$input->id = 9999999;
		$tag->id = 0; // an empty id is invalid

		$repo->get($input->id)->willReturn($tag);

		$this->shouldThrow('Ushahidi\Exception\NotFoundException')->duringInteract($input);
	}

	function it_fails_when_not_allowed($repo, $auth, ReadTagData $input, Tag $tag)
	{
		$input->id = 1;
		$tag->id = 1;

		$repo->get($input->id)->willReturn($tag);
		$auth->isAllowed($tag, 'get')->willReturn(false);

		// the exception will ask for the user id
		$auth->getUserId()->shouldBeCalled();

		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_can_read_a_tag($repo, $auth, ReadTagData $input, Tag $tag)
	{
		$input->id = 1;
		$tag->id = 1;

		$repo->get($input->id)->willReturn($tag);
		$auth->isAllowed($tag, 'get')->willReturn(true);

		$this->interact($input)->shouldReturn($tag);
	}
}
