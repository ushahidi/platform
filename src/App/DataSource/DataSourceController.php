<?php

namespace Ushahidi\App\DataSource;

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataSource
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Post;
use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ramsey\Uuid\Uuid;

abstract class DataSourceController extends Controller
{

    protected $source;

    public function __construct(DataSourceManager $manager, DataSourceStorage $storage)
    {
        $this->source = $manager->getSource($this->source);
        $this->storage = $storage;
    }

    abstract public function handleRequest(Request $request);

    protected function getPendingMessages($limit)
    {
        // Get All "Sent" SMSSync messages
        // Limit it to 20 MAX and FIFO
        $messages = $this->storage->getPendingMessages($limit, $this->source->getId());

        // Grab unassigned messages too (effectively doubles `$limit`)
        $messages += $this->storage->getPendingMessages($limit, null);

        foreach ($messages as $message) {
            if (!$message->data_source_message_id) {
                try {
                    $uuid = Uuid::uuid4();
                    $message->setState([
                        'data_source_message_id' => $uuid->toString()
                    ]);
                } catch (UnsatisfiedDependencyException $e) {
                    // continue
                }
            }

            // Update the message status
            //
            // We don't know if the SMS from the phone itself work or not,
            // but we'll update the messages status to 'unknown' so that
            // its not picked up again
            $this->storage->updateMessageStatus($message->id, MessageStatus::UNKNOWN, $message->data_source_message_id);
        }

        return $messages;
    }

    /**
     * Receive Messages From data provider
     *
     * @param  array  $payload Message payload containing:
     *     - string type    Message type
     *     - string contact_type    Contact type
     *     - string from    From contact
     *     - string message Received Message
     *     - string to      To contact
     *     - string title   Received Message title
     *     - string data_source_message_id Message ID
     * @return void
     */
    protected function save($payload)
    {
        $this->storage->receive(
            $this->source->getId(),
            $payload['type'],
            $payload['contact_type'],
            $payload['from'],
            $payload['message'],
            isset($payload['to']) ? $payload['to'] : null,
            isset($payload['title']) ? $payload['title'] : null,
            isset($payload['datetime']) ? $payload['datetime'] : null,
            isset($payload['data_source_message_id']) ? $payload['data_source_message_id'] : null,
            [],
            $this->source->getInboundFormId(),
            $this->source->getInboundFieldMappings()
        );
    }
}
