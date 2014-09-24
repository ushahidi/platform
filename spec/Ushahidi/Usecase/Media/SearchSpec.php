<?php

namespace spec\Ushahidi\Usecase\Media;

use Ushahidi\Usecase\Media\SearchMediaData;
use Ushahidi\Usecase\Media\SearchMediaRepository;

use Ushahidi\Entity\Media;
use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SearchSpec extends ObjectBehavior
{
	function let(SearchMediaRepository $repo, Authorizer $auth)
	{
		$this->beConstructedWith($repo, $auth);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\Media\Search');
	}

	function it_can_search_for_media($repo, $auth, SearchMediaData $input, Media $media)
	{
		$params  = [];
		$results = [$media];

		$input->getSortingParams()->willReturn($params);
		$repo->setSearchParams($input, $params)->willReturn($repo);
		$repo->getSearchResults()->willReturn($results);
		$auth->isAllowed($media, 'get')->willReturn(true);

		$this->interact($input)->shouldReturn($results);
	}

	function it_removes_disallowed_media($repo, $auth, SearchMediaData $input, Media $media, Media $disallowed)
	{
		$params  = [];
		$results = [$media, $disallowed];
		$allowed = [$media];

		$input->getSortingParams()->willReturn($params);
		$repo->setSearchParams($input, $params)->willReturn($repo);
		$repo->getSearchResults()->willReturn($results);
		$auth->isAllowed($media, 'get')->willReturn(true);
		$auth->isAllowed($disallowed, 'get')->willReturn(false);

		$this->interact($input)->shouldReturn($allowed);
	}
}
