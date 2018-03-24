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
    public function setFormAttributeRepo(FormAttributeRepository $form_attr_repo)
	{
		$this->form_attr_repo = $form_attr_repo;
	}

    public function setTargetedSurveyStateRepo(TargetedSurveyStateRepository $tss_repo)
    {
        $this->tss_repo = $tss_repo;
    }

    public function handle(EventInterface $event, $event_data = [])
    {
        /* @TODO: determing how should we mark a survey as done —
            i.e., if there a contact has two records in targeted_survey_state, only one should be valid
                OR if a survey is complete, OR if no attributes have been sent,
                then we should assume the last_sent_form_attribute_id can tell us.
                If it's null -- then maybe that should mean it's complete or that nothing should be sent?
        */
        $tss_Entity = $this->tss_repo->getByContactId($event_data['contact_id']);
        Kohana::$log->add(Log::INFO, 'Here is that contact\'s current survey state:'.print_r($tss_Entity, true));

        //@TODO: look at this terrible hack for getting the message repo without an redirection loop
        $message_repo = $event_data['message_repo'];

        //get the next attribute in that form, based on the form and the last_sent_form_attribute_id
        $next_form_attribute = $this->form_attr_repo->getNextByFormAttribute($tss_Entity->form_id, $tss_Entity->last_sent_form_attribute_id);

        Kohana::$log->add(Log::INFO, 'Here is the next form attribute:'.print_r($next_form_attribute, true));

        if($next_form_attribute->getId() > 0)
        {
            $new_message = $message_repo->getEntity();
            // @TODO: grab the post_id from the last message/attribute, attach it to this new message --
            //$post_id = $last_message->post_id;

            $messageState = array(
				'contact_id' => $event_data['contact_id'],
				//'post_id' => $postId,
				'title' => $next_form_attribute->label,
				'message' => $next_form_attribute->label,
				'status' => 'pending',
			);
			$new_message->setState($messageState);
			$new_message_id = $message_repo->create($new_message);
			if (!$new_message_id) {
				Kohana::$log->add( Log::ERROR, 'Could not create new message for contact_id: '.print_r($event_data['contact_id'], true));
			}
            
            //@TODO: if there is a next message to be sent, update the TargetedSurveyState Entity with new last_sent_form_attribute_id
            $tss_Entity->setState(['last_sent_form_attribute_id' => $next_form_attribute->getId() ] );
            $updated_tss_entity = $this->tss_repo->update($tss_Entity);
            Kohana::$log->add( Log::ERROR, 'Updated TSS Entity: '.print_r($updated_tss_entity, true));

        }

    }
}
