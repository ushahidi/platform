<?php

namespace spec\Ushahidi\Core\Usecase\Message;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Translation\Translator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Formatter;
use Ushahidi\Contracts\Repository\CreateRepository;
use Ushahidi\Contracts\Repository\Entity\ContactRepository;
use Ushahidi\Contracts\Validator;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Usecase\Message\ReceiveMessage;

class ReceiveMessageSpec extends ObjectBehavior
{
    public function let(
        Authorizer $auth,
        Formatter $format,
        Validator $valid,
        CreateRepository $repo,
        ContactRepository $contactRepo,
        Validator $contactValid,
        Dispatcher $dispatcher,
        Translator $translator
    ) {
        $contactRepo->beADoubleOf(CreateRepository::class);

        $this->setAuthorizer($auth);
        $this->setFormatter($format);
        $this->setRepository($repo);
        $this->setContactRepository($contactRepo);
        $this->setValidator($valid);
        $this->setContactValidator($contactValid);
        $this->setDispatcher($dispatcher);
        $this->setTranslator($translator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ReceiveMessage::class);
    }

    private function getPayload()
    {
        return [
            'id' => 1,
            'contact' => 'something',
            'contact_type' => 'sms',
            'from' => 1234,
            'message' => 'Some message',
            'title' => 'msgtitle',
            'data_source' => 'smssync',
        ];
    }

    private function setupMessageEntity($payload, $repo, $entity)
    {
        // Set usecase parameters
        $this->setPayload($payload);
        // Make message have content
        $entity->title = $payload['title'];
        $entity->message = $payload['message'];
        $entity->additional_data = null;

        // Get a new message entity and set its state
        $repo->getEntity()->willReturn($entity);
        $entity->setState($payload + ['status' => 'received', 'direction' => 'incoming'])->willReturn($entity);
    }

    private function tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact)
    {
        // Called by ReceiveMessage::getContactEntity
        $contactRepo->getByContact($payload['from'], $payload['contact_type'])->willReturn($contact);
        $contact->getId()->willReturn($contact_id);
    }

    /*
     * re: github.com/ushahidi/platform/issues/2111
     * Removing this test according to removing irrelevant authorization
     *
    function it_fails_when_authorization_is_denied(
        $auth,
        $repo,
        $contactRepo,
        Entity $entity,
        Contact $contact
    ) {
        $payload = $this->getPayload();
        $contact_id = 3;
        // ... fetch a new entity
        $this->setupMessageEntity($payload, $repo, $entity);
        $this->tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact);

        // ... if authorization fails
        $action = 'receive';
        $auth->isAllowed($entity, $action)->willReturn(false);

        // ... the exception requests data for the error message
        $entity->getResource()->willReturn('messages');
        $entity->getId()->willReturn(1);
        $auth->getUserId()->willReturn(1);
        $this->shouldThrow('Ushahidi\Core\Exception\AuthorizerException')->duringInteract();
    }
    */

    public function it_fails_when_validation_fails(
        $auth,
        $repo,
        $contactRepo,
        $valid,
        $contactValid,
        Entity $entity,
        Contact $contact
    ) {
        $payload = $this->getPayload();
        $contact_id = 3;
        // ... fetch a new entity
        $this->setupMessageEntity($payload, $repo, $entity);
        $this->tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact);

        // ... convert entity to array for validation
        $entity_array = ['entity' => 'as_array'];
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);

        // ... if authorization passes
        $action = 'receive';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... but validation fails
        $valid->check($entity_array)->willReturn(false);

        // ... the exception requests the errors for the message
        $entity->getResource()->willReturn('messages');
        $valid->errors()->willReturn([]);
        $this->shouldThrow('Ushahidi\Core\Exception\ValidatorException')->duringInteract();
    }

    public function it_fails_when_contact_validation_fails(
        $auth,
        $repo,
        $contactRepo,
        $valid,
        $contactValid,
        Entity $entity,
        Contact $contact
    ) {
        $payload = $this->getPayload();
        $contact_id = 3;
        // ... fetch a new entity
        $this->setupMessageEntity($payload, $repo, $entity);
        $this->tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact);

        // ... convert entity to array for validation
        $entity_array = ['entity' => 'as_array'];
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);
        // ... convert contact to array for validation
        $contact_array = ['entity' => 'as_array'];
        $contact->asArray()->shouldBeCalled()->willReturn($contact_array);

        // ... if authorization passes
        $action = 'receive';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... and message validation passes
        $valid->check($entity_array)->willReturn(true);

        // ... but contact validation fails
        $contactValid->check($contact_array)->willReturn(false);

        // ... the exception requests the errors for the contact
        $contact->getResource()->willReturn('contacts');
        $contactValid->errors()->willReturn([]);
        $this->shouldThrow(ValidatorException::class)->duringInteract();
    }

    public function it_creates_a_new_record(
        $auth,
        $repo,
        $contactRepo,
        $valid,
        $contactValid,
        $format,
        Entity $entity,
        Entity $created,
        Contact $contact
    ) {
        $payload = $this->getPayload();
        $contact_id = 3;
        // ... fetch a new entity
        $this->setupMessageEntity($payload, $repo, $entity);
        $this->tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact);

        // ... convert entity to array for validation
        $entity_array = ['entity' => 'as_array'];
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);
        // ... convert contact to array for validation
        $contact_array = ['entity' => 'as_array'];
        $contact->asArray()->shouldBeCalled()->willReturn($contact_array);

        // ... if authorization passes
        $action = 'receive';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... and validation passes
        $valid->check($entity_array)->willReturn(true);
        $contactValid->check($contact_array)->willReturn(true);

        // .. contact id is set
        $entity->setState(['contact_id' => $contact_id])->willReturn($entity);

        // ... create a new record
        $id = 1;
        $repo->create($entity)->willReturn($id);
        // ..  id is set
        $entity->setState(['id' => $id])->willReturn($entity);

        // ... and returns it
        $this->interact()->shouldReturn($id);
    }

    public function it_creates_a_new_message_and_contact(
        $auth,
        $repo,
        $contactRepo,
        $valid,
        $contactValid,
        $format,
        Entity $entity,
        Entity $created,
        Contact $contact
    ) {
        $payload = $this->getPayload();
        $contact_id = 3;
        // ... fetch a new entity
        $this->setupMessageEntity($payload, $repo, $entity);
        $this->tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact);

        // Create new contact
        $contactRepo->getEntity()->willReturn($contact);
        $contact->setState([
            'contact' => $payload['from'],
            'type' => $payload['contact_type'],
            'data_source' => $payload['data_source'],
        ])->willReturn($contact);

        // ... convert entity to array for validation
        $entity_array = ['entity' => 'as_array'];
        $entity->asArray()->shouldBeCalled()->willReturn($entity_array);
        // ... convert contact to array for validation
        $contact_array = ['entity' => 'as_array'];
        $contact->asArray()->shouldBeCalled()->willReturn($contact_array);

        // ... if authorization passes
        $action = 'receive';
        $auth->isAllowed($entity, $action)->willReturn(true);

        // ... and validation passes
        $valid->check($entity_array)->willReturn(true);
        $contactValid->check($contact_array)->willReturn(true);

        // ... A contact is created
        $contactRepo->create($contact)->willReturn($contact_id);
        $entity->setState(['contact_id' => $contact_id])->willReturn($entity);

        // ... create a new record
        $id = 1;
        $repo->create($entity)->willReturn($id);
        // ..  id is set
        $entity->setState(['id' => $id])->willReturn($entity);

        // ... and returns it
        $this->interact()->shouldReturn($id);
    }
}
