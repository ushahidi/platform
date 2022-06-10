<?php

/**
 * Unit tests for MessageRepository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\Repository;

use Ushahidi\App\Repository\MessageRepository;
use Ushahidi\Core\Entity\Message;
use Tests\TestCase;
use Tests\DatabaseTransactions;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class MessageRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateMany()
    {
        // Generate message data
        $message1 = new Message([
            'title' => 'A title',
            'message' => 'Yo!',
            'data_source_message_id' => '11',
            'data_source' => 'email',
            'type' => 'email',
            'direction' => 'incoming',
            'status' => 'received',
            'additional_data' => [
                'somedata'
            ]
        ]);
        $message2 = new Message([
            'title' => null,
            'message' => 'A message',
            'data_source_message_id' => '33',
            'data_source' => 'twitter',
            'type' => 'twitter',
            'direction' => 'incoming',
            'status' => 'received',
            'additional_data' => [
                [
                    'things' => 'stuff'
                ]
            ]
        ]);
        $message3 = new Message([
            'title' => null,
            'message' => 'Message message message',
            'data_source_message_id' => '55',
            'data_source' => null,
            'type' => 'sms',
            'direction' => 'outgoing',
            'status' => 'sent',
            'additional_data' => [
                'somedata'
            ]
        ]);

        $repo = service('repository.message');
        $inserted = $repo->createMany(collect([
            $message1,
            $message2,
            $message3,
        ]));

        $this->assertCount(3, $inserted);
        $this->seeInOhanzeeDatabase('messages', [
            'id' => $inserted[0],
            'title' => 'A title',
            'data_source_message_id' => '11',
            'data_source' => 'email',
            'type' => 'email',
            'direction' => 'incoming',
            'status' => 'received',
            'additional_data' => '["somedata"]'
        ]);
        $this->seeInOhanzeeDatabase('messages', [
            'id' => $inserted[1],
            'title' => null,
            'message' => 'A message',
            'data_source_message_id' => '33',
            'data_source' => 'twitter',
            'type' => 'twitter',
            'direction' => 'incoming',
            'status' => 'received',
            'additional_data' => '[{"things":"stuff"}]'
        ]);
        $this->seeInOhanzeeDatabase('messages', [
            'id' => $inserted[2],
            'message' => 'Message message message',
            'data_source_message_id' => '55',
            'data_source' => null,
            'type' => 'sms',
            'direction' => 'outgoing',
            'status' => 'sent',
            'additional_data' => '["somedata"]'
        ]);
    }
}
