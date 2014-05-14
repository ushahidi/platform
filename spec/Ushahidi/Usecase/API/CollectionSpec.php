<?php

namespace spec\Ushahidi\Usecase\API;

use Ushahidi\Entity;
use Ushahidi\Usecase\API\CollectionRepository;

use PhpSpec\ObjectBehavior;

class CollectionSpec extends ObjectBehavior
{
	function let(CollectionRepository $repo, Entity $entity)
	{
		$entity->beConstructedWith([]);
		$entity->id = 1;

		$this->beConstructedWith($repo);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\API\Collection');
	}

	function it_can_search_for_entities($repo, $entity)
	{
		$repo->search($entity, ['foo' => 'bar'], ['id' => 'desc'], 2, 3)->shouldBeCalled()->willReturn([1]);

		$this
			->query('foo', 'bar')
			->orderBy('id', 'desc')
			->limit(2)
			->offset(3)
			->search($entity)
				->shouldReturn([1]);
	}

	function it_can_read_entities($repo, $entity)
	{
		$repo->read($entity)->shouldBeCalled()->willReturn(true);
		$this->read($entity)->shouldReturn(true);
	}

	function it_can_create_entities($repo, $entity)
	{
		$repo->create($entity)->shouldBeCalled()->willReturn(true);
		$this->create($entity)->shouldReturn(true);
	}

	function it_can_update_entities($repo, $entity)
	{
		$repo->update($entity)->shouldBeCalled()->willReturn(true);
		$this->update($entity)->shouldReturn(true);
	}

	function it_can_delete_entities($repo, $entity)
	{
		$repo->delete($entity)->shouldBeCalled()->willReturn(true);
		$this->delete($entity)->shouldReturn(true);
	}
}
