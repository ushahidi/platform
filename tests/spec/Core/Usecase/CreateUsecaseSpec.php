<?php

namespace spec\Ushahidi\Core\Usecase;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Translation\Translator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Formatter;
use Ushahidi\Contracts\Repository\CreateRepository;
use Ushahidi\Contracts\Validator;

class CreateUsecaseSpec extends ObjectBehavior
{
    public function let(
        Authorizer $auth,
        Formatter $format,
        Validator $valid,
        CreateRepository $repo,
        Dispatcher $dispatcher,
        Translator $translator
    ) {
        $this->setAuthorizer($auth);
        $this->setFormatter($format);
        $this->setRepository($repo);
        $this->setValidator($valid);
        $this->setDispatcher($dispatcher);
        $this->setTranslator($translator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Ushahidi\Core\Usecase\CreateUsecase');
    }

    private function tryGetEntity($repo, $entity)
    {
        // Set usecase parameters
        $payload = ['create' => true];
        $this->setPayload($payload);

        // Called by CreateUsecase::getEntity
        $repo->getEntity($payload)->willReturn($entity);
    }

    public function it_fails_when_authorization_is_denied($auth, $repo, Entity $entity)
    {
        // ... fetch a new entity
        $this->tryGetEntity($repo, $entity);

        // ... if authorization fails
        $action = 'create';
        $auth->isAllowed($entity, $action)->willReturn(false);

        // ... the exception requests data for the error message
        $entity->getResource()->willReturn('widgets');
        $entity->getId()->willReturn(1);
        $auth->getUserId()->willReturn(1);
        $this->shouldThrow('Ushahidi\Core\Exception\AuthorizerException')->duringInteract();
    }

    public function it_fails_when_validation_fails($auth, $repo, $valid, Entity $entity)
    {
        // ... fetch a new entity
        $this->tryGetEntity($repo, $entity);
        $entity_array = ['entity' => 'as_array'];
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);

        // ... if authorization passes
        $action = 'create';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... but validation fails
        $valid->check($entity_array)->willReturn(false);

        // ... the exception requests the errors for the message
        $entity->getResource()->willReturn('widgets');
        $valid->errors()->willReturn([]);
        $this->shouldThrow('Ushahidi\Core\Exception\ValidatorException')->duringInteract();
    }

    public function it_creates_a_new_record($auth, $repo, $valid, $format, Entity $entity, Entity $created)
    {
        // ... fetch a new entity
        $this->tryGetEntity($repo, $entity);
        $entity_array = ['entity' => 'as_array'];
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);

        // ... if authorization passes
        $action = 'create';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... and validation passes
        $valid->check($entity_array)->willReturn(true);

        // ... create a new record
        $id = 1;
        $repo->create($entity)->willReturn($id);

        // ... fetch the record
        $repo->get($id)->willReturn($created);

        // ... and verify that the record can be read
        $action = 'read';
        $auth->isAllowed($created, $action)->willReturn(true);

        // ... then format the record
        $formatted = ['id' => 1];
        $format->__invoke($created)->willReturn($formatted);

        // ... and returns it
        $this->interact()->shouldReturn($formatted);
    }
}
