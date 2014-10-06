<?php

namespace spec\Ushahidi\Usecase;

use Ushahidi\Usecase\UpdateRepository;

use Ushahidi\Data;
use Ushahidi\Entity;

use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UpdateUsecaseSpec extends ObjectBehavior
{
	function let(Validator $valid, Authorizer $auth, UpdateRepository $repo)
	{
		// usecases are constructed with an array of named tools
		$this->beConstructedWith(compact('valid', 'auth', 'repo'));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\UpdateUsecase');
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
		// (set some data of the entity in question)
		$entity->id  = 1;
		$entity->foo = 'dog';
	
		// (set the input values, with a change)
		$input->id  = 1;
		$input->foo = 'cat';

		// fetch the entity from the repository
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'update';

		// ... and if it fails
		$auth->isAllowed($entity, $action)->willReturn(false);

		// ... the exception requests the userid for the message
		$auth->getUserId()->willReturn(1);
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_fails_when_validation_fails($valid, $auth, $repo, Data $input, Data $update, Entity $entity)
	{
		// (set some data of the entity in question)
		$entity->id  = 1;
		$entity->foo = 'dog';
	
		// (set the input values, with a change)
		$input->id  = 1;
		$input->foo = 'cat';

		// (set the change as the update)
		$update->foo = $input->foo;

		// ... fetch the entity from the repository
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'update';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... compare the entity data
		$entity_array = ['id' => 11, 'foo' => 'dog'];
		$entity->asArray()->willReturn($entity_array);

		// ... with the input to determine what has changed
		$input->getDifferent($entity_array)->willReturn($update);

		// ... when validation fails
		$valid->check($update)->willReturn(false);

		// ... the exception requests the errors for the message
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_updates_a_record($valid, $auth, $repo, Data $input, Data $update, Entity $entity)
	{
		// (set some data of the entity in question)
		$entity->id  = 1;
		$entity->foo = 'dog';
	
		// (set the input values, with a change)
		$input->id  = 1;
		$input->foo = 'cat';

		// (set the change as the update)
		$update->foo = $input->foo;

		// ... fetch the entity from the repository
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'update';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... compare the entity data
		$entity_array = ['id' => 11, 'foo' => 'dog'];
		$entity->asArray()->willReturn($entity_array);

		// ... with the input to determine what has changed
		$input->getDifferent($entity_array)->willReturn($update);

		// ... if validation is successful...
		$valid->check($update)->willReturn(true);

		// ... those changes are stored
		$changed = ['foo' => $update->foo];
		$update->asArray()->willReturn($changed);

		// ... then write the changes
		$repo->update($entity->id, $update)->shouldBeCalled();

		// ... fetch the updated entity from the repository
		$entity->foo = $input->foo;
		$repo->get($input->id)->willReturn($entity);

		// ... and verify that the record can be read
		$action = 'read';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... finally returning the updated record
		$this->interact($input)->shouldReturn($entity);
	}
}
