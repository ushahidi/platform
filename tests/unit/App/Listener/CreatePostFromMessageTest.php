<?php

namespace Tests\Unit\App\Listener;

use Ushahidi\App\Listener\CreatePostFromMessage;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;
use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class CreatePostFromMessageTest extends TestCase
{
    public function setUp()
    {
        parent::setup();

        $this->messageRepo = M::mock(MessageRepository::class);
        $this->targetedSurveyStateRepo = M::mock(TargetedSurveyStateRepository::class);
        $this->postRepo = M::mock(PostRepository::class);

        $this->listener = new CreatePostFromMessage(
            $this->messageRepo,
            $this->targetedSurveyStateRepo,
            $this->postRepo
        );
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testCreatesSimplePost()
    {
        $id = 1;
        $contact_id = 2;
        $message = new Message([
            'id' => $id,
            'contact_id' => $contact_id,
            'message' => 'A message!',
            'title' => 'A title',
            'datetime' => new \DateTime('2018-01-04 00:01:02'),
            'type' => 'sms',
        ]);
        $post = new Post;
        // $inbound_form_id = 7;
        // $inbound_fields = [];

        $this->targetedSurveyStateRepo
            ->shouldReceive('isContactInActiveTargetedSurveyAndReceivedMessage')
            ->with($contact_id)
            ->andReturn(false);

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

        $this->listener->handle(
            $id,
            $message,
            null,
            []
            // $inbound_form_id,
            // $inbound_fields
        );

        $this->assertEquals('A message!', $post->content);
        $this->assertEquals('A title', $post->title);
        $this->assertNull($post->form_id);
        $this->assertEquals(new \DateTime('2018-01-04 00:01:02'), $post->post_date);
        $this->assertEquals(88, $message->post_id);
    }

    public function testCreatesPostAndMapsFields()
    {
        $id = 1;
        $contact_id = 2;
        $message = new Message([
            'id' => $id,
            'contact_id' => $contact_id,
            'message' => 'A message!',
            'title' => 'A title',
            'datetime' => new \DateTime('2018-01-04 00:01:02'),
            'type' => 'sms',
            'additional_data' => [
                'location' => [[
                    'type' => 'Point',
                    'coordinates' => [2.3, -1.4],
                ]],
            ],
        ]);
        $post = new Post;
        $inbound_form_id = 7;
        $inbound_fields = [
            'Title' => 'put-title-here',
            'Date' => 'date-field',
            'Location' => 'location-field'
        ];

        $this->targetedSurveyStateRepo
            ->shouldReceive('isContactInActiveTargetedSurveyAndReceivedMessage')
            ->with($contact_id)
            ->andReturn(false);

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

        $this->listener->handle(
            $id,
            $message,
            $inbound_form_id,
            $inbound_fields
        );

        $this->assertEquals('A message!', $post->content, 'Post content was not set');
        $this->assertEquals('A title', $post->title, 'Post title was not set');
        $this->assertEquals($inbound_form_id, $post->form_id, 'Post form id was not set');
        //var_dump($post->values);
        $this->assertEquals([
            'message_location' => [
                ['lon' => 2.3, 'lat' => -1.4]
            ],
            'put-title-here' => ['A title'],
            'date-field' => ['2018-01-04 00:01:02'],
            'location-field' => [
                ['lon' => 2.3, 'lat' => -1.4]
            ],
        ], $post->values, 'Message data is not mapped to post values');
        $this->assertEquals(new \DateTime('2018-01-04 00:01:02'), $post->post_date);
        $this->assertEquals(88, $message->post_id);
    }
}
