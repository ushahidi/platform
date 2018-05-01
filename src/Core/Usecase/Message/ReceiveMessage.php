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
    protected $outgoingMessageValidator;

	/**
	 * @var CreateRepository
	 */
	protected $contact_repo;

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
	 * @param $survey_state_entity
	 * @return int|$incomingMessageId
	 */
	private function createIncomingMessage($incoming_message, $survey_state_entity)
	{
		// Update state with post id
		$incoming_message->setState(['post_id' => $survey_state_entity->post_id]);
		$incomingMessageId = $this->repo->create($incoming_message);

		return $incomingMessageId;
	}

	/**
     * 	Create the message will be sent next to the targeted survey user
     *
	 * @param $contact_id
	 * @param $survey_state_entity
	 * @param $next_form_attribute
	 * @return int|$outgoingMessageId
	 */
	private function createOutgoingMessage($contact_id, $survey_state_entity, $next_form_attribute, $data_provider)
	{
        // @FIXME: Message type should be configurable per deployment,survey
        $message_type = 'sms';
		// Create new message to send next question to the user
		$outgoingMessage = $this->repo->getEntity()->setState([
			'contact_id' => $contact_id,
			'post_id' => $survey_state_entity->post_id,
			'title' => $next_form_attribute->label,
			'message' => $next_form_attribute->label,
			'status' => Message::PENDING,
			'type' => $message_type,
			'data_provider' => $data_provider,
			'direction' => Message::OUTGOING
		]);

		// Verify its valid
		// @todo not sure we even need to bother. If its not valid, all is lost.
		if (!$this->outgoingMessageValidator->check($outgoingMessage->asArray())) {
			$this->validatorError($outgoingMessage);
		}

		// Save the message
		$outgoingMessageId = $this->repo->create($outgoingMessage);

		// But then continue anyway
		return $outgoingMessageId;
	}

	/**
	 * @param $incoming_message
	 */
	private function createTargetedSurveyMessages($incoming_message)
	{
		// Load the survey state for this contact
		$surveyStateEntity = $this->targeted_survey_state_repo->getActiveByContactId($incoming_message->contact_id);

		// Attempt to load the previous message
		$messageInSurveyState = $this->repo->get($surveyStateEntity->message_id);

		// If we didn't find an outgoing message
		if (!$messageInSurveyState || $messageInSurveyState->direction !== \Ushahidi\Core\Entity\Message::OUTGOING) {
			// We can't save it as a message of the survey
			// ... log an error because we should probably never end up here
			\Kohana::$log->add(
				\Log::ERROR,
				'Could not add contact\'s  message',
				[
					'contact_id' => $incoming_message->contact_id,
					'form_id' => $surveyStateEntity->form_id
				]
			);

			// Create a new post
			$post_id = $this->createPost($incoming_message);
			$incoming_message->setState(compact('post_id'));

			// But always save the message anyway - otherwise its lost forever
			return $this->repo->create($incoming_message);
		}

		// We found the outgoing message... flow continues
		// Save the incoming message
		$incomingMessageId = $this->createIncomingMessage($incoming_message, $surveyStateEntity);

		// Get the next attribute in that form, based on the form and the last_sent_form_attribute_id
		$next_form_attribute = $this->form_attr_repo->getNextByFormAttribute(
			$surveyStateEntity->form_attribute_id
		);

		// Set up intermediate state w/ message id, next attribute and new status
		$surveyStateEntity->setState(
			[
				'form_attribute_id' => $next_form_attribute->getId(),
				'message_id' => $incomingMessageId,
				'survey_status' => Entity\TargetedSurveyState::RECEIVED_RESPONSE
			]
		);
		// And save intermediate state
		$this->targeted_survey_state_repo->update($surveyStateEntity);

		// If we have another question to send
		if ($next_form_attribute->getId() > 0) {
			// Queue next question to be sent
			$outgoingMessageId = $this->createOutgoingMessage(
				$incoming_message->contact_id,
				$surveyStateEntity,
				$next_form_attribute,
				$incoming_message->data_provider
			);

			// If this for some unknown reason fails, log it
			if (!$outgoingMessageId) {
				\Kohana::$log->add(
					\Log::ERROR,
					'Could not create new outgoing message',
					compact('contact_id')
				);

				// Return the incoming message as per usual
				return $incomingMessageId;
			}

			// Update the state with: outgoing message, attribute id, and new status
			$surveyStateEntity->setState(
				[
					'form_attribute_id' => $next_form_attribute->getId(),
					'message_id' => $outgoingMessageId,
					'survey_status' => Entity\TargetedSurveyState::PENDING_RESPONSE
				]
			);

			// And save state
			$this->targeted_survey_state_repo->update($surveyStateEntity);
		} else {
			// Otherwise, No more questions to send
			// Mark survey finished
			$surveyStateEntity->setState(['survey_status' => Entity\TargetedSurveyState::SURVEY_FINISHED]);
			// And save state
			$this->targeted_survey_state_repo->update($surveyStateEntity);
		}

		// Finally, return the new message ID
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
			// @todo decouple this by moving to a listener
			$id = $this->createTargetedSurveyMessages($entity);
		} else {
			// ... create post for message
			// @todo decouple this by moving to a listener
			$post_id = $this->createPost($entity);
			$entity->setState(compact('post_id'));
			// ... persist the new message entity
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
			$contact = $this->contact_repo->getEntity()->setState([
				'contact' => $this->getPayload('from'),
				'type' => $this->getPayload('contact_type'),
				'data_provider' => $this->getPayload('data_provider'),
			]);
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
		$post = $this->post_repo->getEntity()->setState([
			'title' => $message->title,
			'content' => $content,
			'values' => $values,
			'form_id' => $form_id
		]);
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
