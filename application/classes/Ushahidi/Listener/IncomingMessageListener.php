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
//	protected $message_repo;
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
        /* @TODO: determing how should we mark a survey as done —> we don't, right now */

        $targetedSurveyStateEntity = $this->targeted_survey_state_repo->getByContactId($event_data['contact_id']);


        //@FIXME: ugly hack for getting the message repo without a circular reference error
        $message_repo = $event_data['message_repo'];

        //get the next attribute in that form, based on the form and the last_sent_form_attribute_id
        $next_form_attribute = $this->form_attr_repo->getNextByFormAttribute(
        	$targetedSurveyStateEntity->form_id, $targetedSurveyStateEntity->form_attribute_id
		);

        Kohana::$log->add(
        	Log::INFO, 'Here is the next form attribute:'.print_r($next_form_attribute, true)
		);

        if($next_form_attribute->getId() > 0)
        {
            $new_message = $message_repo->getEntity();
            $messageState = array(
				'contact_id' => $event_data['contact_id'],
				'post_id' => $targetedSurveyStateEntity->post_id,
				'title' => $next_form_attribute->label,
				'message' => $next_form_attribute->label,
				'status' => 'pending',
			);
			$new_message->setState($messageState);
			$new_message_id = $message_repo->create($new_message);
			if (!$new_message_id) {
				Kohana::$log->add(
					Log::ERROR, 'Could not create new message for contact_id: '.print_r($event_data['contact_id'], true)
				);
			}

            $targetedSurveyStateEntity->setState(['form_attribute_id' => $next_form_attribute->getId(), 'status' => 'PENDING'] );
            $updated_tss_entity = $this->targeted_survey_state_repo->update($targetedSurveyStateEntity);
            Kohana::$log->add( Log::ERROR, 'Updated TSS Entity: '.print_r($updated_tss_entity, true));

        }

    }
}
