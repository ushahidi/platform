<?php

/**
 * Integration test for ReceiveMessage usecase
 */

namespace Tests\Unit\Core\Usecase;

use Mockery as M;
use Tests\TestCase;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Contracts\Repository\Entity\ContactRepository;
use Ushahidi\Contracts\Repository\Entity\FormAttributeRepository;
use Ushahidi\Contracts\Repository\Entity\MessageRepository;
use Ushahidi\Contracts\Repository\Entity\PostRepository;
use Ushahidi\Contracts\Repository\Entity\TargetedSurveyStateRepository;
use Ushahidi\Core\Entity\Config;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Post;
use Ushahidi\App\Listener\CreatePostFromMessage;
use Ushahidi\App\Listener\HandleTargetedSurveyResponse;
use Ushahidi\App\Subscriber;

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
        $events->subscribe(Subscriber::class);

        //$this->usecase = new ReceiveMessage();
        $this->usecase = service('factory.usecase')->get('messages', 'receive');
        $this->usecase
            ->setRepository($this->messageRepo)
            ->setContactRepository($this->contactRepo)
            ->setDispatcher($events);

        // Ensure smssync is enabled
        // Mock the config repo
        $configRepo = M::mock(ConfigRepository::class);
        // Return email in config
        $configRepo->shouldReceive('get')->with('data-provider')->andReturn(new Config([
            'providers' => [
                'smssync' => true,
            ],
        ]));
        $configRepo->shouldReceive('get')->with('features')->andReturn(new Config([
            'data-providers' => [
                'smssync' => true,
            ],
        ]));
        $this->app->instance(ConfigRepository::class, $configRepo);
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
                'contact' => 1234,
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
                'data_source' => 'smssync',
            ])
            ->interact();
    }
}
