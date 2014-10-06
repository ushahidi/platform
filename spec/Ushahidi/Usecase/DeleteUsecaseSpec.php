<?php

namespace spec\Ushahidi\Usecase;

use Ushahidi\Usecase\DeleteRepository;

use Ushahidi\Data;
use Ushahidi\Entity;

use Ushahidi\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DeleteUsecaseSpec extends ObjectBehavior
{
	function let(Authorizer $auth, DeleteRepository $repo)
	{
		// usecases are constructed with an array of named tools
		$this->beConstructedWith(compact('auth', 'repo'));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\DeleteUsecase');
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
		$action = 'delete';

		// ... and if it fails
		$auth->isAllowed($entity, $action)->willReturn(false);

		// ... the exception requests the userid for the message
		$auth->getUserId()->willReturn(1);
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_deletes_a_record($auth, $repo, Data $input, Entity $entity)
	{
		// (set the entity id)
		$input->id = $entity->id = 1;

		// it fetches the record
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'delete';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... then delete the record
		$repo->delete($input->id)->willReturn(1);

		// ... finally returning the removed record
		$this->interact($input)->shouldReturn($entity);
	}
}
