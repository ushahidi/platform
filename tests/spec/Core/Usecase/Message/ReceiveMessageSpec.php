<?php

namespace spec\Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\CreateRepository;
use Ushahidi\Core\Entity\ContactRepository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReceiveMessageSpec extends ObjectBehavior
{
	function let(
		Authorizer $auth,
		Formatter $format,
		Validator $valid,
		CreateRepository $repo,
		CreateRepository $postRepo,
		ContactRepository $contactRepo,
		Validator $contactValid
	) {
		$contactRepo->beADoubleOf('Ushahidi\Core\Usecase\CreateRepository');

		$this->setAuthorizer($auth);
		$this->setFormatter($format);
		$this->setRepository($repo);
		$this->setContactRepository($contactRepo);
		$this->setPostRepository($postRepo);
		$this->setValidator($valid);
		$this->setContactValidator($contactValid);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Usecase\Message\ReceiveMessage');
	}

	private function getPayload()
	{
		return [
			'contact' => 'something',
			'contact_type' => 'sms',
			'from' => 1234,
			'message' => 'Some message',
			'title' => 'msgtitle',
			'data_provider' => 'smssync'
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
		$entity->setState($payload + ["status" => "received", "direction" => "incoming"])->willReturn($entity);
	}

 	private function tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact)
 	{
		// Called by ReceiveMessage::getContactEntity
		$contactRepo->getByContact($payload['from'], $payload['contact_type'])->willReturn($contact);
		$contact->getId()->willReturn($contact_id);
	}

 	private function createPostEntity($payload, $postRepo, $post)
 	{
		$post_id = 1;
		// Get a new post entity and persist it
		$postRepo->getEntity()->willReturn($post);
		$post->setState(['title' => $payload['title'],
			'content' => $payload['message'],
			'values' => [],
			'form_id' => null
		])->willReturn($post);
		//$postRepo->create($post)->willReturn($post_id);
 	}

	function it_fails_when_authorization_is_denied(
		$auth,
		$repo,
		$contactRepo,
		$postRepo,
		Entity $entity,
		Contact $contact,
		Post $post
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

	function it_fails_when_validation_fails(
		$auth,
		$repo,
		$contactRepo,
		$postRepo,
		$valid,
		$contactValid,
		Entity $entity,
		Contact $contact,
		Post $post
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

	function it_fails_when_contact_validation_fails(
		$auth,
		$repo,
		$contactRepo,
		$postRepo,
		$valid,
		$contactValid,
		Entity $entity,
		Contact $contact,
		Post $post
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
		$this->shouldThrow('Ushahidi\Core\Exception\ValidatorException')->duringInteract();
	}

	function it_creates_a_new_record(
		$auth,
		$repo,
		$contactRepo,
		$postRepo,
		$valid,
		$contactValid,
		$format,
		Entity $entity,
		Entity $created,
		Contact $contact,
		Post $post
	) {
		$payload = $this->getPayload();
		$contact_id = 3;
		// ... fetch a new entity
		$this->setupMessageEntity($payload, $repo, $entity);
		$this->tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact);
		$this->createPostEntity($payload, $postRepo, $post);

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

		// ... a post record is created
		$post_id = 7;
		$postRepo->create($post)->willReturn($post_id);

		// .. contact and post id are set
		$entity->setState(['contact_id' => $contact_id])->willReturn($entity);
		$entity->setState(['post_id' => $post_id])->willReturn($entity);

		// ... create a new record
		$id = 1;
		$repo->create($entity)->willReturn($id);

		// ... and returns it
		$this->interact()->shouldReturn($id);
	}

	function it_creates_a_new_message_and_contact(
		$auth,
		$repo,
		$contactRepo,
		$postRepo,
		$valid,
		$contactValid,
		$format,
		Entity $entity,
		Entity $created,
		Contact $contact,
		Post $post
	) {
		$payload = $this->getPayload();
		$contact_id = 3;
		// ... fetch a new entity
		$this->setupMessageEntity($payload, $repo, $entity);
		$this->tryLoadContactEntity($payload, false, $contactRepo, $contact);
		$this->createPostEntity($payload, $postRepo, $post);

		// Create new contact
		$contactRepo->getEntity()->willReturn($contact);
		$contact->setState([
			'contact' => $payload['from'],
			'type' => $payload['contact_type'],
			'data_provider' => $payload['data_provider'],
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

		// ... a post record is created
		$post_id = 7;
		$postRepo->create($post)->willReturn($post_id);
		$entity->setState(['post_id' => $post_id])->willReturn($entity);

		// ... create a new record
		$id = 1;
		$repo->create($entity)->willReturn($id);

		// ... and returns it
		$this->interact()->shouldReturn($id);
	}
}
