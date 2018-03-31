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

        //get the next attribute in that form, based on the form and the last_sent_form_attribute_id
        $next_form_attribute = $this->form_attr_repo->getNextByFormAttribute(
        	$surveyStateEntity->form_id, $surveyStateEntity->form_attribute_id
		);

        if($next_form_attribute->getId() > 0)
        {	// create message that we received
			$newMessage = $messageRepo->getEntity();
            $messageState = array(
				'contact_id' => $event_data['contact_id'],
				'post_id' => $surveyStateEntity->post_id,
				'title' => $next_form_attribute->label,
				'message' => $next_form_attribute->label,
				'status' => 'received',
			);
			$newMessage->setState($messageState);
			$newMessageId = $messageRepo->create($newMessage);
			if (!$newMessageId) {
				Kohana::$log->add(
					Log::ERROR, 'Could not create new message for contact_id: '.print_r($event_data['contact_id'], true)
				);
			}

            $surveyStateEntity->setState(['form_attribute_id' => $next_form_attribute->getId(), 'status' => 'PENDING'] );
			$updatedTargetedSurveyState = $this->targeted_survey_state_repo->update($surveyStateEntity);
            Kohana::$log->add( Log::ERROR, 'Updated TSS Entity: '.print_r($updatedTargetedSurveyState, true));
        }
    }
}
