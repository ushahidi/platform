<?php

namespace spec\Ushahidi\Core\Usecase;

use Illuminate\Contracts\Translation\Translator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Formatter;
use Ushahidi\Contracts\Repository\UpdateRepository;
use Ushahidi\Contracts\Validator;

class UpdateUsecaseSpec extends ObjectBehavior
{
    public function let(
        Authorizer $auth,
        Formatter $format,
        Validator $valid,
        UpdateRepository $repo,
        Translator $translator
    ) {
        $this->setAuthorizer($auth);
        $this->setFormatter($format);
        $this->setValidator($valid);
        $this->setRepository($repo);
        $this->setTranslator($translator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Ushahidi\Core\Usecase\UpdateUsecase');
    }

    private function tryGetEntity($repo, $entity, $id)
    {
        $payload = ['update' => true];

        // Set usecase parameters
        $this->setIdentifiers(['id' => $id]);
        $this->setPayload($payload);

        // Called by UpdateUsecase::getEntity
        $repo->get($id)->willReturn($entity);
        $entity->setState($payload)->willReturn($entity);

        // Called by UpdateUsecase::verifyEntityLoaded
        $entity->getId()->willReturn($id);
    }

    public function it_fails_when_the_entity_is_not_found($repo, Entity $entity)
    {
        $id = 0;

        // ... fetch the entity
        $this->tryGetEntity($repo, $entity, $id);

        // ... or at least it tried to
        $entity->getResource()->willReturn('widgets');
        $this->shouldThrow('Ushahidi\Core\Exception\NotFoundException')->duringInteract();
    }

    public function it_fails_when_authorization_is_denied($auth, $repo, Entity $entity)
    {
        $id = 1;

        // ... fetch the entity
        $this->tryGetEntity($repo, $entity, 1);

        // ... if authorization fails
        $action = 'update';
        $auth->isAllowed($entity, $action)->willReturn(false);

        // ... the exception requests data the message
        $entity->getResource()->willReturn('widgets');
        $entity->getId()->willReturn($id);
        $auth->getUserId()->willReturn(1);
        $this->shouldThrow('Ushahidi\Core\Exception\AuthorizerException')->duringInteract();
    }

    public function it_fails_when_validation_fails($auth, $repo, $valid, Entity $entity)
    {
        $id = 2;

        // ... fetch the entity
        $this->tryGetEntity($repo, $entity, $id);
        $entity_array = ['entity' => 'changed'];
        $entity->getChanged()->shouldBeCalled()->willReturn($entity_array);
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);

        // ... if authorization passes
        $action = 'update';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... but validation fails
        $valid->check($entity_array, $entity_array)->willReturn(false);

        // ... the exception requests the errors for the message
        $entity->getResource()->willReturn('widgets');
        $valid->errors()->willReturn([]);
        $this->shouldThrow('Ushahidi\Core\Exception\ValidatorException')->duringInteract();
    }

    public function it_updates_the_record($auth, $valid, $repo, $format, Entity $entity, Entity $updated)
    {
        $id = 3;

        // ... fetch the entity
        $this->tryGetEntity($repo, $entity, $id);
        $entity_array = ['entity' => 'changed'];
        $entity->getChanged()->shouldBeCalled()->willReturn($entity_array);
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);

        // ... if authorization passes
        $action = 'update';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... and validation passes
        $valid->check($entity_array, $entity_array)->willReturn(true);

        // ... store the changes
        $repo->update($entity)->shouldBeCalled();

        // ... and verify that the record can be read
        $action = 'read';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... then format the record
        $formatted = ['id' => $id];
        $format->__invoke($entity)->willReturn($formatted);

        // ... and returns it
        $this->interact()->shouldReturn($formatted);
    }
}
