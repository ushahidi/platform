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

use Illuminate\Support\Facades\Log;
use Ushahidi\Contracts\Repository\Entity\ContactRepository;
use Ushahidi\Contracts\Repository\Entity\MessageRepository;
use Ushahidi\Core\Usecase\Message\ReceiveMessage;

class DataSourceStorage
{

    protected $receiveUsecase;
    protected $messageRepo;

    public function __construct(
        ReceiveMessage $receiveUsecase,
        ContactRepository $contactRepo,
        MessageRepository $messageRepo
    ) {
        $this->receiveUsecase = $receiveUsecase;
        $this->messageRepo = $messageRepo;

        $receiveUsecase->setRepository($messageRepo);
        $receiveUsecase->setContactRepository($contactRepo);
    }

    /**
     * Receive Messages From data source
     *
     * @todo  convert params to some kind of DTO
     *
     * @param  string type    Message type
     * @param  string from    From contact
     * @param  string message Received Message
     * @param  string to      To contact
     * @param  string title   Received Message title
     * @param  string data_source_message_id Message ID
     * @return array|void
     */
    public function receive(
        $source_id,
        $type,
        $contact_type,
        $from,
        $message,
        $to = null,
        $title = null,
        $datetime = null,
        $data_source_message_id = null,
        array $additional_data = null,
        $inbound_form_id = null,
        array $inbound_fields = null
    ) {
        $data_source = $source_id;

        $payload = compact([
            'type',
            'from',
            'message',
            'to',
            'title',
            'datetime',
            'data_source_message_id',
            'data_source',
            'contact_type',
            'additional_data',
            // Pass data for mapping inbound fields
            // @todo these could come directly from the source but it ended up in a circular dependency
            'inbound_form_id',
            'inbound_fields'
        ]);
        try {
            return $this->receiveUsecase->setPayload($payload)
                ->interact();
        } catch (\Ushahidi\Core\Exception\NotFoundException $e) {
            report($e);
            Log::debug($e->getMessage(), $payload);
            if (!app()->runningInConsole()) {
                abort(404, $e->getMessage());
            }
        } catch (\Ushahidi\Core\Exception\AuthorizerException $e) {
            report($e);
            Log::error($e->getMessage(), $payload);
            if (!app()->runningInConsole()) {
                abort(403, $e->getMessage());
            }
        } catch (\Ushahidi\Core\Exception\ValidatorException $e) {
            report($e);
            $payload['errors'] = $e->getErrors();
            Log::debug($e->getMessage(), $payload);
            if (!app()->runningInConsole()) {
                abort(422, 'Validation Error: ' . $e->getMessage() . '; ' .  implode(', ', $e->getErrors()));
            }
        } catch (\InvalidArgumentException $e) {
            report($e);
            Log::debug($e->getMessage(), $payload);
            if (!app()->runningInConsole()) {
                abort(400, 'Bad request: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            report($e);
            Log::debug($e->getMessage(), $payload);
            if (!app()->runningInConsole()) {
                abort(500, 'Internal Server Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get pending messages for source
     *
     * @param  string  $source  data source id
     * @param  boolean $limit   maximum number of messages to send at a time
     */
    public function getPendingMessages($limit = 20, $source = false)
    {
        // Grab latest messages
        // @todo reformat messages so we're not leaking Message entities
        return $this->messageRepo->getPendingMessages($source, $limit);
    }

    /**
     * Get pending messages for type
     *
     * @param  string  $source  data source id
     * @param  boolean $limit   maximum number of messages to send at a time
     */
    public function getPendingMessagesByType($limit = 20, $type = false)
    {
        // Grab latest messages
        // @todo reformat messages so we're not leaking Message entities
        return $this->messageRepo->getPendingMessagesByType($type, $limit);
    }

    /**
     * Update message status
     *
     * @param  [type] $id          [description]
     * @param  [type] $status      [description]
     * @param  [type] $tracking_id [description]
     * @return [type]              [description]
     */
    public function updateMessageStatus($id, $status, $tracking_id = null)
    {
        // @todo validate message status
        $this->messageRepo->updateMessageStatus($id, $status, $tracking_id);
    }
}
