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


    public function receiveExceptionsProvider()
    {
        return [
            [
                "\Ushahidi\Core\Exception\NotFoundException",
                "Symfony\Component\HttpKernel\Exception\NotFoundHttpException",
                404,
                ""
            ],
            [
                "\Ushahidi\Core\Exception\AuthorizerException",
                "Symfony\Component\HttpKernel\Exception\HttpException",
                403,
                ""
            ],
            [
                "\Ushahidi\Core\Exception\ValidatorException",
                "Symfony\Component\HttpKernel\Exception\HttpException",
                422,
                "Validation Error: ; sorry my fault, but you are also not perfect"
            ],
            [
                "\InvalidArgumentException",
                "Symfony\Component\HttpKernel\Exception\HttpException",
                400,
                "Bad request: ; sorry my fault, but you are also not perfect"
            ]
        ];
    }

    /**
     * @dataProvider receiveExceptionsProvider
     * @param $thrownE string exception name to be thrown by interact()
     * @param $expectedE string exception that is expected to be thrown by receive()
     * @param $expectedStatusCode int status code of the exception thrown by receive()
     * @param $expectedMessage string message of the exception thrown by receive()
     */
    public function testFailedReceive($thrownE, $expectedE, $expectedStatusCode, $expectedMessage)
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

        $e = M::mock($thrownE);
        $e->allows()->getErrors()->andReturns(["sorry my fault", "but you are also not perfect"]);

        $this->usecase
            ->shouldReceive('interact')
            ->once()
            ->andThrow($e);

        try {
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
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame($expectedStatusCode, $exception->getStatusCode());
            $this->assertSame($expectedE, get_class($exception));
            $this->assertSame($expectedMessage, $exception->getMessage());
        }
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
