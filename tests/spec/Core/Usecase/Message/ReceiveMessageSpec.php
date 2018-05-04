<?php

namespace spec\Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\CreateRepository;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\TargetedSurveyState;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;

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
		Validator $contactValid,
		FormAttributeRepository $formAttributeRepo,
		TargetedSurveyStateRepository $targetedSurveyStateRepo,
		Validator $outgoingMessageValid
	) {
		$contactRepo->beADoubleOf('Ushahidi\Core\Usecase\CreateRepository');
		$targetedSurveyStateRepo->beADoubleOf('Ushahidi\Core\Usecase\UpdateRepository');

		$this->setAuthorizer($auth);
		$this->setFormatter($format);
		$this->setRepository($repo);
		$this->setContactRepository($contactRepo);
		$this->setPostRepository($postRepo);
		$this->setValidator($valid);
		$this->setContactValidator($contactValid);
		$this->setFormAttributeRepo($formAttributeRepo);
		$this->setTargetedSurveyStateRepo($targetedSurveyStateRepo);
		$this->setOutgoingMessageValidator($outgoingMessageValid);
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
		$contactRepo->isInActiveTargetedSurvey($contact_id)->willReturn((false));
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
		$this->tryLoadContactEntity($payload, $contact_id, $contactRepo, $contact);
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

	function it_updates_post_and_send_next_message(
		$auth,
		$repo,
		$contactRepo,
		$postRepo,
		$valid,
		$contactValid,
		$format,
		$formAttributeRepo,
		$targetedSurveyStateRepo,
		$outgoingMessageValid
	) {
		$payload = $this->getPayload();
		$contact_id = 23;
		$previous_message_id = 77;
		$next_attr_id = 78;
		// ... fetch a new entity

		// Set usecase parameters
		$this->setPayload($payload);

		// Get a new message entity and set its state
		$repo->getEntity()->will(function () {
			return new Message();
		});

		// Create new contact
		$contactRepo->getEntity()->will(function () {
			return new Contact();
		});

		// ... if authorization passes
		$action = 'receive';
		$auth
			->isAllowed(Argument::type(Message::class), $action)
			->willReturn(true);

		// ... and validation passes
		$valid->check([
			'id' => null,
			'parent_id' => null,
			'contact_id' => null,
			'post_id' => null,
			'user_id' => null,
			'data_provider' => "smssync",
			'data_provider_message_id' => null,
			'title' => "msgtitle",
			'message' => "Some message",
			'datetime' => null,
			'type' => null,
			'status' => "received",
			'direction' => "incoming",
			'created' => null,
			'additional_data' => null,
			'notification_post_id' => null,
		])->willReturn(true);

		// ... A contact is loaded
		$contactRepo
			->getByContact($payload['from'], $payload['contact_type'])
			->will(function () use ($payload, $contact_id) {
				return new Contact([
					'id' => $contact_id,
					'contact' => $payload['from'],
					'type' => $payload['contact_type']
				]);
			});

		// ... and contact is verified
		$contactValid
			->check([
				"id" => $contact_id,
				"user_id" => null,
				"data_provider" => null,
				"type" => "sms",
				"contact" => "1234",
				"created" => null,
				"updated" => null,
				"can_notify" => null,
				"country_code" => null
			])
			->willReturn(true);

		// ... then contact is found in a targeted survey
		$contactRepo->isInActiveTargetedSurvey($contact_id)->willReturn(true);

		// the targeted survey is loaded
		$targetedSurveyStateRepo
			->getActiveByContactId($contact_id)
			->will(function () use ($previous_message_id, $next_attr_id) {
				return new TargetedSurveyState([
					'id' => 1,
					'message_id' => $previous_message_id,
					'form_attribute_id' => $next_attr_id
				]);
			});

		// previous message is loaded
		$repo->get($previous_message_id)->willReturn(new Message([
			'id' => $previous_message_id,
			'direction' => 'outgoing'
		]));

		// Save the incoming message
		$messageId = 1;
		$repo->create(Argument::type(Message::class))->willReturn($messageId);

		// Get next attribute
		$formAttributeRepo
			->getNextByFormAttribute($next_attr_id)
			->willReturn(new FormAttribute([
				'id' => $next_attr_id
			]));

		$targetedSurveyStateRepo->update(Argument::type(TargetedSurveyState::class))->willReturn(1);

		$outgoingMessageValid
			->check([
				"id" => null,
				"parent_id" => null,
				"contact_id" => 23,
				"post_id" => null,
				"user_id" => null,
				"data_provider" => "smssync",
				"data_provider_message_id" => null,
				"title" => null,
				"message" => null,
				"datetime" => null,
				"type" => "sms",
				"status" => "pending",
				"direction" => "outgoing",
				"created" => null,
				"additional_data" => null,
				"notification_post_id" => null
			])
			->willReturn(true);

		// return message ID
		$this->interact()->shouldReturn($messageId);
	}
}
