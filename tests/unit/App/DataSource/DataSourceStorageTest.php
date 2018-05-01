<?php

/**
 * Tests for DataSourceStorage class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource;

use Tests\TestCase;
use Mockery as M;

use Ushahidi\App\DataSource\DataSourceStorage;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\Entity\Message;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class DataSourceStorageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->usecase = M::mock(Usecase::class);
        $this->messageRepo = M::mock(MessageRepository::class);
    }

    public function testReceive()
    {
        $storage = new DataSourceStorage($this->usecase, $this->messageRepo);

        $this->usecase
            ->shouldReceive('setPayload')->once()
            ->with([
                'data_source' => 'smssync',
                'type' => 'sms',
                'contact_type' => 'phone',
                'from' => 123456,
                'message' => 'Yo dawg I heard you like messages, so I put some messages in your messages',
                'to' => "YOU!",
                'title' => null,
                'datetime' => null,
                'data_source_message_id' => null,
                'additional_data' => null,
                'inbound_form_id' => 1,
                'inbound_fields' => ['Title' => 'somekey']
            ])
            ->andReturn($this->usecase);

        $this->usecase
            ->shouldReceive('interact')->once()
            ->andReturn([
                'id' => 1
            ]);

        $result = $storage->receive(
            'smssync',
            'sms',
            'phone',
            123456,
            'Yo dawg I heard you like messages, so I put some messages in your messages',
            "YOU!",
            null,
            null,
            null,
            null,
            1,
            ['Title' => 'somekey']
        );

        $this->assertEquals(['id' => 1], $result);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testFailedReceive()
    {
        $storage = new DataSourceStorage($this->usecase, $this->messageRepo);

        $this->usecase
            ->shouldReceive('setPayload')
            ->once()
            ->with([
                'data_source' => 'smssync',
                'type' => 'sms',
                'contact_type' => 'phone',
                'from' => 123456,
                'message' => 'Yo dawg I heard you like messages, so I put some messages in your messages',
                'to' => "YOU!",
                'title' => null,
                'datetime' => null,
                'data_source_message_id' => null,
                'additional_data' => null,
                'inbound_form_id' => 1,
                'inbound_fields' => ['Title' => 'somekey']
            ])
            ->andReturn($this->usecase);

        $e = M::spy(\Ushahidi\Core\Exception\NotFoundException::class);

        $this->usecase
            ->shouldReceive('interact')
            ->once()
            ->andThrow($e);

        $storage->receive(
            'smssync',
            'sms',
            'phone',
            123456,
            'Yo dawg I heard you like messages, so I put some messages in your messages',
            "YOU!",
            null,
            null,
            null,
            null,
            1,
            ['Title' => 'somekey']
        );

        $e->shouldHaveReceived('getMessage')->once();

        // @todo test other errors and validate error message
    }

    public function testGetPendingMessages()
    {
        $storage = new DataSourceStorage($this->usecase, $this->messageRepo);

        // Test default params
        $this->messageRepo
            ->shouldReceive('getPendingMessages')
            ->once()
            ->with(false, 20)
            ->andReturn([new Message([])]);

        $result = $storage->getPendingMessages();
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Message::class, $result[0]);

        // Test custom params
        $this->messageRepo
            ->shouldReceive('getPendingMessages')
            ->once()
            ->with('smssync', 21)
            ->andReturn([new Message([])]);

        $result = $storage->getPendingMessages(21, 'smssync');
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Message::class, $result[0]);
    }

    public function testGetPendingMessagesByType()
    {
        $storage = new DataSourceStorage($this->usecase, $this->messageRepo);

        // Test default params
        $this->messageRepo
            ->shouldReceive('getPendingMessagesByType')
            ->once()
            ->with(false, 20)
            ->andReturn([new Message([])]);

        $result = $storage->getPendingMessagesByType();
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Message::class, $result[0]);

        // Test custom params
        $this->messageRepo
            ->shouldReceive('getPendingMessagesByType')
            ->once()
            ->with('sms', 21)
            ->andReturn([new Message([])]);

        $result = $storage->getPendingMessagesByType(21, 'sms');
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Message::class, $result[0]);
    }

    public function testUpdateMessageStatus()
    {
        $storage = new DataSourceStorage($this->usecase, $this->messageRepo);

        // Test default params
        $this->messageRepo
            ->shouldReceive('updateMessageStatus')
            ->once()
            ->with(7, 'failed', 'magicid');

        $storage->updateMessageStatus(7, 'failed', 'magicid');
    }
}
