<?php

namespace Ushahidi\DataSource;

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataSource
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ramsey\Uuid\Uuid;

abstract class DataSourceController extends Controller
{
    /**
     * @var string | \Ushahidi\DataSource\Contracts\DataSource
     */
    protected $source;

    /**
     * @var \Ushahidi\DataSource\DataSourceStorage
     */
    protected $storage;

    public function __construct(DataSourceManager $manager, DataSourceStorage $storage)
    {
        $this->storage = $storage;

        try {
            $this->source = $manager->getEnabledSource($this->source);
        } catch (\InvalidArgumentException $e) {
        }
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
                } catch (\Exception $e) {
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
        $source_id =  $this->source;

        if (!is_string($this->source)) {
            $source_id = $this->source->getId();
            $inbound_form_id = $this->source->getInboundFormId();
            $inbound_field_mappings = $this->source->getInboundFieldMappings();
        }

        $this->storage->receive(
            $source_id,
            $payload['type'],
            $payload['contact_type'],
            $payload['from'],
            $payload['message'],
            isset($payload['to']) ? $payload['to'] : null,
            isset($payload['title']) ? $payload['title'] : null,
            isset($payload['datetime']) ? $payload['datetime'] : null,
            isset($payload['data_source_message_id']) ? $payload['data_source_message_id'] : null,
            [],
            $inbound_form_id ?? false,
            $inbound_field_mappings ?? []
        );
    }
}
