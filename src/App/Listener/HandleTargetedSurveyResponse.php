<?php
namespace Ushahidi\App\Listener;

use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\TargetedSurveyState;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;
use Ushahidi\Core\Entity\FormAttributeRepository;

class HandleTargetedSurveyResponse
{

    protected $messageRepo;
    protected $contactRepo;
    protected $formAttrRepo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        MessageRepository $messageRepo,
        TargetedSurveyStateRepository $targetedSurveyStateRepo,
        FormAttributeRepository $formAttrRepo
    ) {
        $this->targetedSurveyStateRepo = $targetedSurveyStateRepo;
        $this->messageRepo = $messageRepo;
        $this->formAttrRepo = $formAttrRepo;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle($incomingMessageId, $incomingMessage, $inbound_form_id, $inbound_fields)
    {
        // \Log::info('Running HandleTargetedSurveyResponse', func_get_args());

        // check if contact is part of an open targeted_survey
        if (!$this->targetedSurveyStateRepo
            ->isContactInActiveTargetedSurveyAndReceivedMessage($incomingMessage->contact_id)
        ) {
            return;
        }

        // Load the survey state for this contact
        $surveyStateEntity = $this->targetedSurveyStateRepo->getActiveByContactId($incomingMessage->contact_id);

        // We found the outgoing message... flow continues
        // Save the incoming message
        $incomingMessage->setState(['post_id' => $surveyStateEntity->post_id]);
        $this->messageRepo->update($incomingMessage);

        // Get the next attribute in that form, based on the form and the last_sent_form_attribute_id
        $nextFormAttribute = $this->formAttrRepo->getNextByFormAttribute(
            $surveyStateEntity->form_attribute_id
        );

        // Set up intermediate state w/ message id, next attribute and new status
        $surveyStateEntity->setState(
            [
                'form_attribute_id' => $nextFormAttribute->getId(),
                'message_id' => $incomingMessageId,
                'survey_status' => TargetedSurveyState::RECEIVED_RESPONSE
            ]
        );
        // And save intermediate state
        $this->targetedSurveyStateRepo->update($surveyStateEntity);

        // If we have another question to send
        if ($nextFormAttribute->getId() > 0) {
            // Queue next question to be sent
            $outgoingMessageId = $this->createOutgoingMessage(
                $incomingMessage->contact_id,
                $surveyStateEntity,
                $nextFormAttribute,
                $incomingMessage->data_source
            );

            // If this for some unknown reason fails, log it
            if (!$outgoingMessageId) {
                \Log::error(
                    'Could not create new outgoing message',
                    compact('contact_id')
                );

                // Return the incoming message as per usual
                return false;
            }

            // Update the state with: outgoing message, attribute id, and new status
            $surveyStateEntity->setState(
                [
                    'form_attribute_id' => $nextFormAttribute->getId(),
                    'message_id' => $outgoingMessageId,
                    'survey_status' => TargetedSurveyState::PENDING_RESPONSE
                ]
            );

            // And save state
            $this->targetedSurveyStateRepo->update($surveyStateEntity);
        } else {
            // Otherwise, No more questions to send
            // Mark survey finished
            $surveyStateEntity->setState(['survey_status' => TargetedSurveyState::SURVEY_FINISHED]);
            // And save state
            $this->targetedSurveyStateRepo->update($surveyStateEntity);
        }

        // Prevent Create Post listener form running
        return false;
    }

    /**
     * @param $contact_id
     * @param $survey_state_entity
     * @param $nextFormAttribute
     * @return int|$outgoingMessageId
     */
    private function createOutgoingMessage($contact_id, $surveyStateEntity, $nextFormAttribute, $data_source)
    {
        // Create new message to send next question to the user
        $outgoingMessage = $this->messageRepo->getEntity()->setState([
            'contact_id' => $contact_id,
            'post_id' => $surveyStateEntity->post_id,
            'title' => $nextFormAttribute->label,
            'message' => $nextFormAttribute->label,
            'status' => Message::PENDING,
            'type' => 'sms', // FIXME
            'data_source' => $data_source,
            'direction' => Message::OUTGOING
        ]);

        // Not bothering to verify its valid since there no way
        // for a use to remedy it at this point

        // Save the message
        $outgoingMessageId = $this->messageRepo->create($outgoingMessage);

        // But then continue anyway
        return $outgoingMessageId;
    }
}
