<?php

namespace spec\Ushahidi\Usecase\Media;

use Ushahidi\Usecase\Media\ReadMediaData;
use Ushahidi\Usecase\Media\ReadMediaRepository;

use Ushahidi\Entity\Media;
use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReadSpec extends ObjectBehavior
{
	function let(ReadMediaRepository $repo, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Media\Read');
	}

	function it_fails_when_the_media_does_not_exist($repo, ReadMediaData $input, Media $media)
	{
		$input->id = 9999999;
		$media->id = 0; // an empty id is invalid

		$repo->get($input->id)->willReturn($media);

		$this->shouldThrow('Ushahidi\Exception\NotFoundException')->duringInteract($input);
	}

	function it_fails_when_not_allowed($repo, $auth, ReadMediaData $input, Media $media)
	{
		$input->id = 1;
		$media->id = 1;

		$repo->get($input->id)->willReturn($media);
		$auth->isAllowed($media, 'get')->willReturn(false);

		// the exception will ask for the user id
		$auth->getUserId()->shouldBeCalled();

		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_can_read_a_media($repo, $auth, ReadMediaData $input, Media $media)
	{
		$input->id = 1;
		$media->id = 1;

		$repo->get($input->id)->willReturn($media);
		$auth->isAllowed($media, 'get')->willReturn(true);

		$this->interact($input)->shouldReturn($media);
	}
}
