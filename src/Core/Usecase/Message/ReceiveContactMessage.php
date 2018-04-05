<?php

/**
 * Create Message Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\CreateUsecase;

class ReceiveContactMessage extends CreateUsecase
{
	/**
	 * Get an empty entity, apply the payload.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		return $this->repo->getEntity()->setState($this->payload + [
				'status' => Message::RECEIVED,
				'direction' => Message::INCOMING
			]);
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
		$incomingMessageState['contact_id']= $contact_id;
		$incomingMessageState['post_id']= $survey_state_entity->post_id;
		$incomingMessage->setState($incomingMessageState);
		$incomingMessageId = $incomingMessageRepo->create($incomingMessage);
		if (!$incomingMessageId) {
			Kohana::$log->add(
				Log::ERROR, 'Could not create new incoming message for contact_id: '.print_r($contact_id, true)
			);
			throw new HTTP_Exception_400('Could not create new incoming message for contact_id: '. $contact_id);
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
		// create message that we will send to thhe user next
		$newMessage = $this->repo->getEntity();
		$messageState = array(
			'contact_id' => $contact_id,
			'post_id' => $survey_state_entity->post_id,
			'title' => $next_form_attribute->label,
			'message' => $next_form_attribute->label,
			'status' => 'received'
		);
		$newMessage->setState($messageState);
		$newMessageId = $this->repo->create($newMessage);
		if (!$newMessageId) {
			Kohana::$log->add(
				Log::ERROR, 'Could not create new message for contact_id: '.print_r($contact_id, true)
			);
			throw new HTTP_Exception_400('Could not create new outgoing message for contact_id: '. $contact_id);
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
		/* @TODO: mark survey as done in targeted_survey state
		 * if last survey question was answered by contact
		 */
		$surveyStateEntity = $this->targeted_survey_state_repo->getByContactId($contact_id);
		$messageInSurveyState = clone $this->repo;
		// ... attempt to load the entity
		$messageInSurveyState = $messageInSurveyState->get($surveyStateEntity->message_id);
		if (!$messageInSurveyState || $messageInSurveyState->direction !== \Ushahidi\Core\Entity\Message::OUTGOING) {
			//we can't save it as a message of the survey
			Kohana::$log->add(
				Log::ERROR,
				'Could not add contact\'s  message for contact_id: ' .
				print_r($contact_id, true) . ' and form '.$surveyStateEntity->form_id
			);
			throw new HTTP_Exception_400(
				'Outgoing question not found for contact ' . $contact_id . ' and form '.$surveyStateEntity->form_id
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
				'survey_status' => 'RECEIVED RESPONSE'
			]
		);
		$this->targeted_survey_state_repo->update($surveyStateEntity);
		if ($next_form_attribute->getId() > 0) {
			$newMessageId = $this->createOutgoingMessage($contact_id, $surveyStateEntity, $next_form_attribute);
			$surveyStateEntity->setState(
				[
					'form_attribute_id' => $next_form_attribute->getId(),
					'message_id' => $newMessageId,
					'survey_status' => 'PENDING RESPONSE'
				]
			);
			$this->targeted_survey_state_repo->update($surveyStateEntity);
		} else {
			$surveyStateEntity->setState(['survey_status' => 'SURVEY FINISHED'] );
			$this->targeted_survey_state_repo->update($surveyStateEntity);
		}
		return $surveyStateEntity;
	}
}
