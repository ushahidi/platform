<?php

namespace spec\Ushahidi\Core\Usecase;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Usecase\DeleteRepository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DeleteUsecaseSpec extends ObjectBehavior
{
	function let(Authorizer $auth, Formatter $format, DeleteRepository $repo)
	{
		$this->setAuthorizer($auth);
		$this->setFormatter($format);
		$this->setRepository($repo);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Usecase\DeleteUsecase');
	}

	function it_fails_when_no_identifer_exists()
	{
		$this->shouldThrow('InvalidArgumentException')->duringInteract();
	}

	private function tryGetEntity($repo, $entity, $id)
	{
		// Set usecase parameters
		$this->setIdentifiers(['id' => $id]);

		// Called by DeleteUsecase::getEntity
		$repo->get($id)->willReturn($entity);

		// Called by NotFoundException and AuthorizerException
		$entity->getId()->willReturn($id);
		$entity->getResource()->willReturn('widgets');
	}

	function it_fails_when_the_entity_is_not_found($repo, Entity $entity)
	{
		$id = 0;

		// ... fetch the entity
		$this->tryGetEntity($repo, $entity, $id);

		// ... or at least it tried to
		$this->shouldThrow('Ushahidi\Core\Exception\NotFoundException')->duringInteract();
	}

	function it_fails_when_authorization_is_denied($auth, $repo, Entity $entity)
	{
		$id = 1;

		// ... fetch the entity
		$this->tryGetEntity($repo, $entity, $id);

		// ... if authorization fails
		$action = 'delete';
		$auth->isAllowed($entity, $action)->willReturn(false);

		// ... the exception requests the userid for the message
		$auth->getUserId()->willReturn(1);
		$this->shouldThrow('Ushahidi\Core\Exception\AuthorizerException')->duringInteract();
	}

	function it_deletes_and_formats_a_record($auth, $repo, $format, Entity $entity)
	{
		$id = 2;

		// ... fetch the entity
		$this->tryGetEntity($repo, $entity, $id);

		// ... if authorization passes
		$action = 'delete';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... it deletes the record
		$repo->delete($entity)->shouldBeCalled();

		// ... if it can be read
		$action = 'read';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... it formats the record
		$formatted = ['id' => $id];
		$format->__invoke($entity)->willReturn($formatted);

		// ... and returns it
		$this->interact()->shouldReturn($formatted);
	}
}
