<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Incoming Message Listener
 *
 * Listens for incoming messages,
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Event\AbstractListener;
use League\Event\EventInterface;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;
use Ushahidi\Core\Entity\MessageRepository;

class Ushahidi_Listener_IncomingMessageListener extends AbstractListener
{
	protected $form_attr_repo;
	protected $targeted_survey_state_repo;
//	protected $messageRepo;
//
//	public function setMessageRepo(MessageRepository $repo) {
//		$this->message_repo = $repo;
//	}

    public function setFormAttributeRepo(FormAttributeRepository $repo)
	{
		$this->form_attr_repo = $repo;
	}

    public function setTargetedSurveyStateRepo(TargetedSurveyStateRepository $repo)
    {
        $this->targeted_survey_state_repo = $repo;
    }

    public function handle(EventInterface $event, $event_data = [])
    {
        /* @TODO: mark survey as done in targeted_survey state
		 * if last survey question was answered by contact
		 */

        $surveyStateEntity = $this->targeted_survey_state_repo->getByContactId($event_data['contact_id']);

        /**
		 * @FIXME: ugly hack for getting the message repo without a circular reference error
		 * (becase Message repo has this listener injected)
		*/
        $messageRepo = $event_data['message_repo'];

		$messageInSurveyState = clone $messageRepo;

		// ... attempt to load the entity
		$messageInSurveyState = $messageInSurveyState->get($surveyStateEntity->message_id);
		if (!$messageInSurveyState || $messageInSurveyState->direction !== \Ushahidi\Core\Entity\Message::OUTGOING) {
			//we can't save it as a message of the survey
			Kohana::$log->add(
				Log::ERROR, 'Could add contact\'s  message for contact_id: '.print_r($event_data['contact_id'], true) . ' and form '.$surveyStateEntity->form_id
			);
			throw new Exception('Outgoing question not found for contact ' . $event_data['contact_id'] . ' and form '.$surveyStateEntity->form_id);
		}
        //get the next attribute in that form, based on the form and the last_sent_form_attribute_id
        $next_form_attribute = $this->form_attr_repo->getNextByFormAttribute(
        	$surveyStateEntity->form_id, $surveyStateEntity->form_attribute_id
		);

		//create incoming messagge
		$incomingMessageRepo = clone $messageRepo;
		$incomingMessage = $incomingMessageRepo->getEntity();
		$incomingMessageState = $event_data['incoming_message']->asArray();
		$incomingMessageState['contact_id']= $event_data['contact_id'];
		$incomingMessageState['post_id']= $surveyStateEntity->post_id;
		$incomingMessage->setState($incomingMessageState);
		$incomingMessageId = $incomingMessageRepo->create($incomingMessage);
		if (!$incomingMessageId) {
			Kohana::$log->add(
				Log::ERROR, 'Could not create new incoming message for contact_id: '.print_r($event_data['contact_id'], true)
			);
			throw new Exception('Could not create new incoming message for contact_id: '. $event_data['contact_id']);
		}
		$surveyStateEntity->setState(['form_attribute_id' => $next_form_attribute->getId(), 'message_id' => $incomingMessageId, 'survey_status' => 'RECEIVED RESPONSE'] );
		$updatedTargetedSurveyState = $this->targeted_survey_state_repo->update($surveyStateEntity);
        if($next_form_attribute->getId() > 0)
        {
        	// create message that we will send to thhe user next
			$newMessage = $messageRepo->getEntity();
            $messageState = array(
				'contact_id' => $event_data['contact_id'],
				'post_id' => $surveyStateEntity->post_id,
				'title' => $next_form_attribute->label,
				'message' => $next_form_attribute->label,
				'status' => 'received'
			);
			$newMessage->setState($messageState);
			$newMessageId = $messageRepo->create($newMessage);
			if (!$newMessageId) {
				Kohana::$log->add(
					Log::ERROR, 'Could not create new message for contact_id: '.print_r($event_data['contact_id'], true)
				);
				throw new Exception('Could not create new outgoing message for contact_id: '. $event_data['contact_id']);
			}
			$surveyStateEntity->setState(['form_attribute_id' => $next_form_attribute->getId(), 'message_id' => $newMessageId, 'survey_status' => 'PENDING RESPONSE'] );
			$updatedTargetedSurveyState = $this->targeted_survey_state_repo->update($surveyStateEntity);
		} else {
			$surveyStateEntity->setState(['survey_status' => 'SURVEY FINISHED'] );
			$updatedTargetedSurveyState = $this->targeted_survey_state_repo->update($surveyStateEntity);
		}

		Kohana::$log->add( Log::ERROR, 'Updated TSS Entity: '.print_r($updatedTargetedSurveyState, true));
    }
}
