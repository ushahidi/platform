<?php

/**
 * Ushahidi Platform Receive Message Use Case
 *
 * - Takes a received SMS message
 * - finds/creates the associated contact
 * - Stores the raw message
 * - Creates a new un-typed post from the message
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Usecase\CreateRepository;

use Ushahidi\Core\Exception\ValidatorException;
use \Log;
use \Kohana;
use HTTP_Exception_400;

class ReceiveMessage extends CreateUsecase
{
	/**
	 * @var CreateRepository
	 */
	protected $post_repo;

	/**
	 * Inject a post repository
	 *
	 * @param  $repo CreateRepository
	 * @return $this
	 */
	public function setPostRepository(CreateRepository $postRepo)
	{
		$this->post_repo = $postRepo;
		return $this;
	}

	protected $targeted_survey_state_repo;
	protected $form_attr_repo;
	/**
	 * @var CreateRepository
	 */
	protected $contact_repo;

	protected $outgoingMessageValidator;


	/**
	 * Inject a contact repository
	 *
	 * @param  $repo CreateRepository
	 * @return $this
	 */
	public function setContactRepository(CreateRepository $contactRepo)
	{
		$this->contact_repo = $contactRepo;
		return $this;
	}

	/**
	 * @var Validator
	 */
	protected $contactValidator;

	/**
	 * Inject a contact validator
	 *
	 * @param  $repo Validator
	 * @return $this
	 */
	public function setContactValidator(Validator $contactValidator)
	{
		$this->contactValidator = $contactValidator;
		return $this;
	}

	/**
	 * Inject a contact validator
	 *
	 * @param  $repo Validator
	 * @return $this
	 */
	public function setOutgoingMessageValidator(Validator $outgoingValidator)
	{
		$this->outgoingMessageValidator = $outgoingValidator;
		return $this;
	}

	public function setFormAttributeRepo(Entity\FormAttributeRepository $repo)
	{
		$this->form_attr_repo = $repo;
	}

	public function setTargetedSurveyStateRepo(Entity\TargetedSurveyStateRepository $repo)
	{
		$this->targeted_survey_state_repo = $repo;
	}

	/**
	 * @param $incoming_message
	 * @param $contact_id
	 * @param $survey_state_entity
	 * @return int|$incomingMessageId
	 * @throws HTTP_Exception_400
	 */
	private function createIncomingMessage($incoming_message, $contact_id, $survey_state_entity)
	{
		//create incoming message
		$incomingMessageRepo = clone $this->repo;
		$incomingMessage = $incomingMessageRepo->getEntity();
		$incomingMessageState = $incoming_message->asArray();
		$incomingMessageState['contact_id'] = $contact_id;
		$incomingMessageState['post_id'] = $survey_state_entity->post_id;
		$incomingMessage->setState($incomingMessageState);

		// ... verify that the message entity is in a valid state
		$this->verifyValid($incomingMessage);
		$incomingMessageId = $incomingMessageRepo->create($incomingMessage);
		if (!$incomingMessageId) {
			Kohana::$log->add(
				Log::ERROR,
				'Could not create new incoming message for contact_id: ' . print_r($contact_id, true)
			);
			throw new HTTP_Exception_400('Could not create new incoming message for contact_id: ' . $contact_id);
		}
		return $incomingMessageId;
	}

	/**
	 * @param $contact_id
	 * @param $survey_state_entity
	 * @param $next_form_attribute
	 * @return int|$incomingMessageId
	 * @throws HTTP_Exception_400
	 */
	private function createOutgoingMessage($contact_id, $survey_state_entity, $next_form_attribute)
	{
        // @FIXME: Message type should be configurable per deployment,survey
        $message_type = 'sms';
        $data_provider = \DataProvider::getEnabledProviderForType($message_type);

		// create message that we will send to the user next
		$newMessage = $this->repo->getEntity();
		$messageState = array(
			'contact_id' => $contact_id,
			'post_id' => $survey_state_entity->post_id,
			'title' => $next_form_attribute->label,
			'message' => $next_form_attribute->label,
			'status' => Message::PENDING,
            'data_provider' => $data_provider,
			'type' => $message_type,
			'direction' => Message::OUTGOING
		);
		$newMessage->setState($messageState);
		$this->outgoingMessageValidator->check($messageState);
		$newMessageId = $this->repo->create($newMessage);
		if (!$newMessageId) {
			Kohana::$log->add(
				Log::ERROR,
				'Could not create new message for contact_id: ' . print_r($contact_id, true)
			);
			throw new HTTP_Exception_400('Could not create new outgoing message for contact_id: ' . $contact_id);
		}
		return $newMessageId;
	}

	/**
	 * @param $contact_id
	 * @param $incoming_message
	 * @throws HTTP_Exception_400
	 */
	private function createTargetedSurveyMessages($contact_id, $incoming_message)
	{
		$surveyStateEntity = $this->targeted_survey_state_repo->getActiveByContactId($contact_id);
		$messageInSurveyState = clone $this->repo;
		// ... attempt to load the entity
		$messageInSurveyState = $messageInSurveyState->get($surveyStateEntity->message_id);
		if (!$messageInSurveyState || $messageInSurveyState->direction !== \Ushahidi\Core\Entity\Message::OUTGOING) {
			//we can't save it as a message of the survey
			Kohana::$log->add(
				Log::ERROR,
				'Could not add contact\'s  message for contact_id: ' .
				print_r($contact_id, true) . ' and form ' . $surveyStateEntity->form_id
			);
			throw new HTTP_Exception_400(
				'Outgoing question not found for contact ' . $contact_id . ' and form ' . $surveyStateEntity->form_id
			);
		}
		//get the next attribute in that form, based on the form and the last_sent_form_attribute_id
		$next_form_attribute = $this->form_attr_repo->getNextByFormAttribute(
			$surveyStateEntity->form_attribute_id
		);
		//create incoming message
		$incomingMessageId = $this->createIncomingMessage($incoming_message, $contact_id, $surveyStateEntity);
		// intermediate state to mark when we receive a message
		$surveyStateEntity->setState(
			[
				'form_attribute_id' => $next_form_attribute->getId(),
				'message_id' => $incomingMessageId,
				'survey_status' => Entity\TargetedSurveyState::RECEIVED_RESPONSE
			]
		);
		$this->targeted_survey_state_repo->update($surveyStateEntity);
		if ($next_form_attribute->getId() > 0) {
			$newMessageId = $this->createOutgoingMessage($contact_id, $surveyStateEntity, $next_form_attribute);
			$surveyStateEntity->setState(
				[
					'form_attribute_id' => $next_form_attribute->getId(),
					'message_id' => $newMessageId,
					'survey_status' => Entity\TargetedSurveyState::PENDING_RESPONSE
				]
			);
			$this->targeted_survey_state_repo->update($surveyStateEntity);
		} else {
			$surveyStateEntity->setState(['survey_status' => Entity\TargetedSurveyState::SURVEY_FINISHED]);
			$this->targeted_survey_state_repo->update($surveyStateEntity);
		}
		return $incomingMessageId;
	}

	// Usecase
	public function interact()
	{
		// Fetch and hydrate the message entity...
		$entity = $this->getEntity();

		// ... verify that the message entity can be created by the current user
		$this->verifyReceiveAuth($entity);

		// ... verify that the message entity is in a valid state
		$this->verifyValid($entity);

		// Find or create contact based on >$this->getPayload('from')
		$contact = $this->getContactEntity();

		// ... verify the contact is valid
		$this->verifyValidContact($contact);

		// ... create contact if it doesn't exist
		$contact_id = $this->createContact($contact);
		$entity->setState(compact('contact_id'));
		$id = null;
		/**
		 * check if contact is part of an open targeted_survey.
		 * If they are, the first post was created already so no need to create a new one
		 */
		if ($this->isContactInTargetedSurvey($contact_id)) {
			$id = $this->createTargetedSurveyMessages($contact_id, $entity);
		} else {
			$post_id = null;
			// don't throw an event
			// ... create post for message
			$post_id = $this->createPost($entity);
			// ... persist the new message entity
			if ($post_id) {
				$entity->setState(compact('post_id'));
			}
			$id = $this->repo->create($entity);
		}
		return $id;
	}

	/**
	 * Get an empty entity, apply the payload.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		return $this->repo->getEntity()->setState(
			$this->payload + [
				'status' => Message::RECEIVED,
				'direction' => Message::INCOMING
			]
		);
	}

	/**
	 * Create contact record for message
	 *
	 * @return Entity $contact
	 */
	protected function getContactEntity()
	{
		// Is the sender of the message a registered contact?
		$contact = $this->contact_repo->getByContact($this->getPayload('from'), $this->getPayload('contact_type'));
		if (!$contact->getId()) {
			// this is the first time a message has been received by this number, so create contact
			$contact = $this->contact_repo->getEntity()->setState(
				[
					'contact' => $this->getPayload('from'),
					'type' => $this->getPayload('contact_type'),
					'data_provider' => $this->getPayload('data_provider'),
				]
			);
		}
		return $contact;
	}

	protected function isContactInTargetedSurvey($contact_id)
	{
		return $this->contact_repo->isInActiveTargetedSurvey($contact_id);
	}

	/**
	 * Create contact (if its new)
	 *
	 * @param  Entity $contact
	 * @return Int
	 */
	protected function createContact(Entity $contact)
	{
		// If contact already existed, just return id.
		if ($contact->getId()) {
			return $contact->getId();
		}

		return $this->contact_repo->create($contact);
	}

	/**
	 * Create post for message
	 *
	 * @param  Entity $message
	 * @return Int
	 */
	protected function createPost(Entity $message)
	{
		$values = [];
		$form_id = null;

		$content = $message->message;

		if ($message->additional_data) {
			if (isset($message->additional_data['form_id'])) {
				$form_id = $message->additional_data['form_id'];
				// Check provider fields for form attribute mapping
				$inbound_fields = $message->additional_data['inbound_fields'];

				if (isset($this->payload['title']) && isset($inbound_fields['Title'])) {
					$values[$inbound_fields['Title']] = array($this->payload['title']);
				}

				if (isset($this->payload['from']) && isset($inbound_fields['From'])) {
					$values[$inbound_fields['From']] = array($this->payload['from']);
				}

				if (isset($this->payload['to']) && isset($inbound_fields['To'])) {
					$values[$inbound_fields['To']] = array($this->payload['to']);
				}

				if (isset($this->payload['message']) && isset($inbound_fields['Message'])) {
					$values[$inbound_fields['Message']] = array($this->payload['message']);
				}

				if (isset($this->payload['date']) && isset($inbound_fields['Date'])) {
					$timestamp = date("Y-m-d H:i:s", strtotime($this->payload['date']));
					$values[$inbound_fields['Date']] = array($timestamp);
				}

				if (isset($message->additional_data['location']) && isset($inbound_fields['Location'])) {
					foreach ($message->additional_data['location'] as $location) {
						if (!empty($location['type'])
							&& !empty($location['coordinates'])
							&& ucfirst($location['type']) == 'Point'
						) {
							$values[$inbound_fields['Location']][] = [
								'lon' => $location['coordinates'][0],
								'lat' => $location['coordinates'][1]
							];
						}
					}
				}
			}
			// Pull locations from extra metadata
			$values['message_location'] = [];
			if (isset($message->additional_data['location'])) {
				foreach ($message->additional_data['location'] as $location) {
					if (!empty($location['type'])
						&& !empty($location['coordinates'])
						&& ucfirst($location['type']) == 'Point'
					) {
						$values['message_location'][] = [
							'lon' => $location['coordinates'][0],
							'lat' => $location['coordinates'][1]
						];
					}
				}
			}
		}
		// First create a post
		$post = $this->post_repo->getEntity()->setState(
			[
				'title' => $message->title,
				'content' => $content,
				'values' => $values,
				'form_id' => $form_id
			]
		);
		return $this->post_repo->create($post);
	}

	protected function verifyValidContact(Entity $contact)
	{
		// validate contact
		if (!$this->contactValidator->check($contact->asArray())) {
			$this->contactValidatorError($contact);
		}
	}

	/**
	 * Throw a ValidatorException
	 *
	 * @param  Entity $entity
	 * @return null
	 * @throws ValidatorException
	 */
	protected function contactValidatorError(Entity $entity)
	{
		throw new ValidatorException(
			sprintf(
				'Failed to validate %s entity',
				$entity->getResource()
			),
			$this->contactValidator->errors()
		);
	}

	/**
	 * Verifies the current user is allowed receive access on $entity
	 *
	 * @param  Entity $entity
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyReceiveAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'receive');
	}
}
