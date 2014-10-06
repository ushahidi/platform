<?php

namespace spec\Ushahidi\Usecase;

use Ushahidi\Usecase\ReadRepository;

use Ushahidi\Data;
use Ushahidi\Entity;

use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReadUsecaseSpec extends ObjectBehavior
{
	function let(Authorizer $auth, ReadRepository $repo)
	{
		// usecases are constructed with an array of named tools
		$this->beConstructedWith(compact('auth', 'repo'));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\ReadUsecase');
	}

	function it_fails_when_the_entity_is_not_found($repo, Data $input, Entity $entity)
	{
		// (set the entity id)
		$input->id  = 9999;
		$entity->id = 0;

		// it fetches the record
		$repo->get($input->id)->willReturn($entity);

		// ... or at least it tried to
		$entity->getResource()->shouldBeCalled();
		$this->shouldThrow('Ushahidi\Exception\NotFoundException')->duringInteract($input);
	}

	function it_fails_when_authorization_is_denied($auth, $repo, Data $input, Entity $entity)
	{
		// (set the entity id)
		$input->id = $entity->id = 1;

		// it fetches the record
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'read';

		// ... and if it fails
		$auth->isAllowed($entity, $action)->willReturn(false);

		// ... the exception requests the userid for the message
		$auth->getUserId()->willReturn(1);
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_reads_a_record($auth, $repo, Data $input, Entity $entity)
	{
		// (set the entity id)
		$input->id = $entity->id = 1;

		// it fetches the record
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'read';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... finally returning the new record
		$this->interact($input)->shouldReturn($entity);
	}
}
