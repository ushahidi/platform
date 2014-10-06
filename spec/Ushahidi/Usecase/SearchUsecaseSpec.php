<?php

namespace spec\Ushahidi\Usecase;

use Ushahidi\Usecase\SearchRepository;

use Ushahidi\Data;
use Ushahidi\Entity;

use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SearchUsecaseSpec extends ObjectBehavior
{
	function let(Authorizer $auth, SearchRepository $repo, Data $search)
	{
		// usecases are constructed with an array of named tools
		$this->beConstructedWith(compact('auth', 'repo'));

		// input data is a search
		$search->beADoubleOf('Ushahidi\SearchData');
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\SearchUsecase');
	}

	function it_searchs_for_multiple_records($auth, $repo, $search, Entity $entity, Entity $result)
	{
		// it searchs for records
		$repo->setSearchParams($search)->shouldBeCalled();

		// ... and fetch the results
		$results = [$result];
		$repo->getSearchResults()->willReturn($results);

		// ... then check that each record can be seen
		$action = 'read';
		foreach ($results as $r) {
			$auth->isAllowed($r, $action)->willReturn(true);
		}

		// ... finally returning the new record
		$this->interact($search)->shouldReturn($results);
	}
}
