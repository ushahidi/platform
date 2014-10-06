<?php

namespace spec\Ushahidi\Usecase;

use Ushahidi\Usecase\CreateRepository;

use Ushahidi\Data;
use Ushahidi\Entity;

use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreateUsecaseSpec extends ObjectBehavior
{
	function let(Validator $valid, Authorizer $auth, CreateRepository $repo)
	{
		// usecases are constructed with an array of named tools
		$this->beConstructedWith(compact('valid', 'auth', 'repo'));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Usecase\CreateUsecase');
	}

	function it_fails_when_validation_fails($valid, Data $input)
	{
		// when validation fails...
		$valid->check($input)->willReturn(false);

		// ... the exception requests the errors for the message
		$valid->errors()->willReturn([]);
		$this->shouldThrow('Ushahidi\Exception\ValidatorException')->duringInteract($input);
	}

	function it_fails_when_authorization_is_denied($valid, $auth, $repo, Data $input, Entity $resource)
	{
		// after validation is successful...
		$valid->check($input)->willReturn(true);

		// ... with an an empty resource / entity
		$repo->getEntity()->willReturn($resource);

		// ... run the authorization action
		$action = 'create';

		// ... and if it fails
		$auth->isAllowed($resource, $action)->willReturn(false);

		// ... the exception requests the userid for the message
		$auth->getUserId()->willReturn(1);
		$this->shouldThrow('Ushahidi\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_creates_a_new_record($valid, $auth, $repo, Data $input, Entity $resource, Entity $created)
	{
		// after validation is successful...
		$valid->check($input)->willReturn(true);

		// ... with an an empty resource / entity
		$repo->getEntity()->willReturn($resource);

		// ... run the authorization action
		$action = 'create';
		$auth->isAllowed($resource, $action)->willReturn(true);

		// ... use the input to create a new record
		$newid  = 2;
		$repo->create($input)->willReturn($newid);

		// ... then fetch the created record
		$repo->get($newid)->willReturn($created);

		// ... and verify that the record can be read
		$action = 'read';
		$auth->isAllowed($created, $action)->willReturn(true);

		// ... finally returning the new record
		$this->interact($input)->shouldReturn($created);
	}
}
