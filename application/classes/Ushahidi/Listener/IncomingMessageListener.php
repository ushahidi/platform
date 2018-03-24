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
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\FormAttributeRepository;

class Ushahidi_Listener_IncomingMessageListener extends AbstractListener
{
    //@TODO: use a targeted_survey_state repo instead of contact repo, probably
    public function setContactRepo(ContactRepository $contact_repo)
	{
		$this->contact_repo = $contact_repo;
	}

    public function setFormAttributeRepo(FormAttributeRepository $form_attr_repo)
	{
		$this->form_attr_repo = $form_attr_repo;
	}

    public function handle(EventInterface $event, $contact_id = null)
    {
        //if part of a targeted survey, then determine if a new message needs to be sent out
        Kohana::$log->add(Log::INFO, 'Lets send the next message to: '.print_r($contact_id, true));

        //@TODO: lookup the last message sent to this contact -- DO THIS WITH TargetedSurveyStateRepository instead!
        $last_form_attribute_id = $this->contact_repo->getLastMessageSentToContact($contact_id);
        Kohana::$log->add(Log::INFO, 'The last attribute ID sent to them was:'.print_r($last_form_attribute_id, true));

        //@TODO: grab the post_id from that message, attach it to this new message
        //$post_id = $last_message->post_id;

        //@TODO: get the next attribute
        //@TODO: send that attribute
        //@TODO: update the last_sent_form_attribute_id with this new message

    }
}
