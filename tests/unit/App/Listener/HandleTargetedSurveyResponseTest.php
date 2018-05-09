<?php

namespace Tests\Unit\App\Listeners;

use Ushahidi\App\Listener\HandleTargetedSurveyResponse;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\TargetedSurveyState;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormAttributeRepository;

use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class HandleTargetedSurveyResponseTest extends TestCase
{

    public function setUp()
    {
        parent::setup();

        $this->messageRepo = M::mock(MessageRepository::class);
        $this->targetedSurveyStateRepo = M::mock(TargetedSurveyStateRepository::class);
        $this->formAttributeRepo = M::mock(FormAttributeRepository::class);

        $this->listener = new HandleTargetedSurveyResponse(
            $this->messageRepo,
            $this->targetedSurveyStateRepo,
            $this->formAttributeRepo
        );
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testReceivesResponse()
    {
        $id = 1;
        $contact_id = 2;
        $message = new Message([
            'id' => $id,
            'contact_id' => $contact_id
        ]);
        $inbound_form_id = null;
        $inbound_fields = [];
        $previous_message_id = 64;
        $outbound_message_id = 65;

        // First check the message is in a targeted survey
        $this->targetedSurveyStateRepo
            ->shouldReceive('isContactInActiveTargetedSurveyAndReceivedMessage')
            ->with($contact_id)
            ->andReturn(true);

        // Then load survey state
        $targetedSurveyState = new TargetedSurveyState([
            'contact_id' => $contact_id,
            'message_id' => $previous_message_id,
            'form_attribute_id' => 32,
            'post_id' => 45,
        ]);
        $this->targetedSurveyStateRepo
            ->shouldReceive('getActiveByContactId')
            ->with($contact_id)
            ->andReturn($targetedSurveyState);

        // Update the message
        $this->messageRepo
            ->shouldReceive('update')
            ->with($message);

        // Load the next question
        $formAttribute = new FormAttribute([
            'id' => 33,
            'label' => 'A question'
        ]);
        $this->formAttributeRepo
            ->shouldReceive('getNextByFormAttribute')
            ->with($targetedSurveyState->form_attribute_id)
            ->andReturn($formAttribute);

        // Save intermediate survey state
        $this->targetedSurveyStateRepo
            ->shouldReceive('update')
            ->with(
                M::on(
                    function ($argument) use ($targetedSurveyState, $id) {
                        if ($targetedSurveyState != $argument) {
                            return false;
                        }
                        if ($id != $argument->message_id) {
                            return false;
                        }
                        if ('RECEIVED RESPONSE' != $argument->survey_status) {
                            return false;
                        }
                        if (33 != $argument->form_attribute_id) {
                            return false;
                        }

                        return true;
                    }
                )
            )
            ->andReturn(1);

        // Create a new outgoing message
        $outgoingMessage = new Message;
        $this->messageRepo
            ->shouldReceive('getEntity')
            ->andReturn($outgoingMessage);

        $this->messageRepo
            ->shouldReceive('create')
            ->with($outgoingMessage)
            ->andReturn($outbound_message_id);

        // Update survey state again
        $this->targetedSurveyStateRepo
            ->shouldReceive('update')
            ->with(
                M::on(
                    function ($argument) use ($targetedSurveyState, $outbound_message_id) {
                        if ($targetedSurveyState != $argument) {
                            return false;
                        }
                        if ($outbound_message_id != $argument->message_id) {
                            return false;
                        }
                        if ('PENDING RESPONSE' != $argument->survey_status) {
                            return false;
                        }

                        return true;
                    }
                )
            )
            ->andReturn(1);

        // Expectations all set
        // Call the handler
        $this->listener->handle(
            $id,
            $message,
            $inbound_form_id,
            $inbound_fields
        );

        // Validate final values were updated
        $this->assertEquals(45, $message->post_id);
        $this->assertEquals('outgoing', $outgoingMessage->direction);
        $this->assertEquals('A question', $outgoingMessage->message);
        $this->assertEquals($contact_id, $outgoingMessage->contact_id);
        $this->assertEquals($outbound_message_id, $targetedSurveyState->message_id);
        $this->assertEquals($formAttribute->id, $targetedSurveyState->form_attribute_id);
        $this->assertEquals('PENDING RESPONSE', $targetedSurveyState->survey_status);
    }

    public function testReceivesFinalResponse()
    {
        $id = 1;
        $contact_id = 2;
        $message = new Message([
            'id' => $id,
            'contact_id' => $contact_id
        ]);
        $inbound_form_id = null;
        $inbound_fields = [];
        $previous_message_id = 64;
        $outbound_message_id = 65;

        // First check the message is in a targeted survey
        $this->targetedSurveyStateRepo
            ->shouldReceive('isContactInActiveTargetedSurveyAndReceivedMessage')
            ->with($contact_id)
            ->andReturn(true);

        // Then load survey state
        $targetedSurveyState = new TargetedSurveyState([
            'contact_id' => $contact_id,
            'message_id' => $previous_message_id,
            'form_attribute_id' => 32,
            'post_id' => 45,
        ]);
        $this->targetedSurveyStateRepo
            ->shouldReceive('getActiveByContactId')
            ->with($contact_id)
            ->andReturn($targetedSurveyState);

        // Update the message
        $this->messageRepo
            ->shouldReceive('update')
            ->with($message);

        // Load the next question
        // But there isn't one
        $formAttribute = new FormAttribute([]);
        $this->formAttributeRepo
            ->shouldReceive('getNextByFormAttribute')
            ->with($targetedSurveyState->form_attribute_id)
            ->andReturn($formAttribute);

        // Save intermediate survey state
        $this->targetedSurveyStateRepo
            ->shouldReceive('update')
            ->with(
                M::on(
                    function ($argument) use ($targetedSurveyState, $id) {
                        if ($targetedSurveyState != $argument) {
                            return false;
                        }
                        if ($id != $argument->message_id) {
                            return false;
                        }
                        if ('RECEIVED RESPONSE' != $argument->survey_status) {
                            return false;
                        }
                        if (null != $argument->form_attribute_id) {
                            return false;
                        }

                        return true;
                    }
                )
            )
            ->andReturn(1);

        // No outgoing message this time
        $this->messageRepo
            ->shouldNotReceive('getEntity');
        $this->messageRepo
            ->shouldNotReceive('create');

        // Update survey state to finished
        $this->targetedSurveyStateRepo
            ->shouldReceive('update')
            ->with(
                M::on(
                    function ($argument) use ($targetedSurveyState, $id) {
                        if ($targetedSurveyState != $argument) {
                            return false;
                        }
                        if ($id != $argument->message_id) {
                            return false;
                        }
                        if ('SURVEY FINISHED' != $argument->survey_status) {
                            return false;
                        }

                        return true;
                    }
                )
            )
            ->andReturn(1);

        // Expectations all set
        // Call the handler
        $this->listener->handle(
            $id,
            $message,
            $inbound_form_id,
            $inbound_fields
        );

        // Validate final values were updated
        $this->assertEquals(45, $message->post_id);
        $this->assertEquals($id, $targetedSurveyState->message_id);
        $this->assertEquals($formAttribute->id, $targetedSurveyState->form_attribute_id);
        $this->assertEquals('SURVEY FINISHED', $targetedSurveyState->survey_status);
    }

    public function testNotInTargetedSurvey()
    {
        $id = 1;
        $contact_id = 2;
        $message = new Message([
            'id' => $id,
            'contact_id' => $contact_id
        ]);
        $inbound_form_id = null;
        $inbound_fields = [];

        // First check the message is in a targeted survey
        $this->targetedSurveyStateRepo
            ->shouldReceive('isContactInActiveTargetedSurveyAndReceivedMessage')
            ->with($contact_id)
            ->andReturn(false);

        // Expectations all set
        // Call the handler
        $this->listener->handle(
            $id,
            $message,
            $inbound_form_id,
            $inbound_fields
        );

        // Validate final values were updated
        $this->assertEquals(null, $message->post_id);
    }
}
