<?php

/**
 * Integration test for ReceiveMessage usecase
 */

namespace Tests\Unit\Core\Usecase;

use Ushahidi\Core\Usecase\ReceiveMessage;

use Ushahidi\App\Listener\CreatePostFromMessage;
use Ushahidi\App\Listener\HandleTargetedSurveyResponse;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostRepository;
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
class ReceiveMessageTest extends TestCase
{

    public function setUp()
    {
        parent::setup();

        $this->messageRepo = M::mock(MessageRepository::class);
        $this->contactRepo = M::mock(ContactRepository::class);
        $this->targetedSurveyStateRepo = M::mock(TargetedSurveyStateRepository::class);
        $this->postRepo = M::mock(PostRepository::class);
        $this->formAttributeRepo = M::mock(FormAttributeRepository::class);

        $this->app->instance(CreatePostFromMessage::class, new CreatePostFromMessage(
            $this->messageRepo,
            $this->targetedSurveyStateRepo,
            $this->postRepo
        ));
        $this->app->instance(HandleTargetedSurveyResponse::class, new HandleTargetedSurveyResponse(
            $this->messageRepo,
            $this->targetedSurveyStateRepo,
            $this->formAttributeRepo
        ));

        $events = app('events');
        $events->subscribe(\Ushahidi\App\Subscriber::class);

        //$this->usecase = new ReceiveMessage();
        $this->usecase = service('factory.usecase')->get('messages', 'receive');
        $this->usecase
            ->setRepository($this->messageRepo)
            ->setContactRepository($this->contactRepo)
            ->setDispatcher($events);

        // Ensure smssync is enabled
        app('datasources')->setEnabledSources(['smssync' => true]);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testReceiveMessage()
    {
        $message = new Message();
        $this->messageRepo
            ->shouldReceive('getEntity')
            ->andReturn($message);

        $this->messageRepo
            ->shouldReceive('create')
            ->with($message)
            ->andReturn(1);

        $this->contactRepo
            ->shouldReceive('getByContact')
            ->with(1234, 'phone')
            ->andReturn(new Contact([
                'id' => 2,
                'type' => 'phone',
                'contact' => 1234
            ]));

        // First check the message is in a targeted survey
        $this->targetedSurveyStateRepo
            ->shouldReceive('isContactInActiveTargetedSurveyAndReceivedMessage')
            ->with(2)
            ->andReturn(false);

        $post = new Post;
        $this->postRepo
            ->shouldReceive('getEntity')
            ->with()
            ->andReturn($post);

        $this->postRepo
            ->shouldReceive('create')
            ->with($post)
            ->andReturn(88);

        $this->messageRepo
            ->shouldReceive('update')
            ->with($message)
            ->andReturn(1);

        $this->usecase
            ->setPayload([
                'message' => 'Some junk',
                'type' => 'sms',
                'from' => 1234,
                'contact_type' => 'phone',
                'data_source' => 'smssync'
            ])
            ->interact();
    }
}
