<?php

/**
 * Ushahidi Message Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository;

use Ohanzee\DB;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Entity\Message;
use Illuminate\Support\Collection;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Contracts\MessageDirection;
use Ushahidi\Contracts\Repository\Usecase\CreateMessageRepository;
use Ushahidi\Contracts\Repository\Usecase\UpdateMessageRepository;
use Ushahidi\Contracts\Repository\Entity\MessageRepository as MessageRepositoryContract;

class MessageRepository extends OhanzeeRepository implements
    MessageRepositoryContract,
    UpdateMessageRepository,
    CreateMessageRepository
{
    // Use the JSON transcoder to encode properties
    use Concerns\JsonTranscode;

    use Concerns\UsesBulkAutoIncrement;

    // OhanzeeRepository
    protected function getTable()
    {
        return 'messages';
    }

    // CreateRepository
    // ReadRepository
    public function getEntity(array $data = null)
    {
        return new Message($data);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return [
            'box', 'status', 'contact', 'parent', 'post', 'type', 'data_source',
            'q' /* LIKE contact, title, message */
        ];
    }

    // Concerns\JsonTranscode
    protected function getJsonProperties()
    {
        return ['additional_data'];
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query
            ->join('contacts')
                ->on('contact_id', '=', 'contacts.id');

        if ($search->box === 'outbox') {
            // Outbox only shows outgoing messages
            $query->where('direction', '=', 'outgoing');
        } elseif ($search->box === 'inbox') {
            // Inbox only shows incoming messages
            $query->where('direction', '=', 'incoming');
        }

        // Get the requested status, which is secondary to box
        $status = $search->status;

        if ($search->box === 'archived') {
            // Archive only shows archived messages
            $query->where('status', '=', 'archived');
        } elseif ($status) {
            if ($status !== 'all') {
                // Search for a specific status
                $query->where('status', '=', $status);
            }
        } else {
            // Other boxes do not display archived
            $query->where('status', '!=', 'archived');
        }

        if ($search->q) {
            $query->and_where_open();
            $query->where('contacts.contact', 'LIKE', "%$search->q%");
            $query->or_where('title', 'LIKE', "%$search->q%");
            $query->or_where('message', 'LIKE', "%$search->q%");
            $query->and_where_close();
        }

        foreach ([
            'contact',
            'parent',
            'post',
        ] as $fk) {
            if ($search->$fk) {
                $query->where("messages.{$fk}_id", '=', $search->$fk);
            }
        }

        foreach ([
            'type',
            'data_source',
        ] as $key) {
            if ($search->$key) {
                $query->where("messages.{$key}", '=', $search->$key);
            }
        }
    }

    // MessageRepository
    public function getPendingMessages($data_source, $limit)
    {
        $status = 'pending';
        $direction = Message::OUTGOING;
        $query = $this->selectQuery(compact('status', 'direction'))
            ->limit($limit)
            ->order_by('created', 'ASC')
            // Include contact in same query
            ->join('contacts', 'LEFT')->on('contacts.id', '=', 'messages.contact_id')
            ->select('contacts.contact')
            ->select(['contacts.type', 'contact_type'])
            ;

        if ($data_source) {
            $query->where('messages.data_source', '=', $data_source);
        }

        $results = $query->execute($this->db());

        return $this->getCollection($results->as_array());
    }

    // MessageRepository
    public function getPendingMessagesByType($type, $limit)
    {
        $status = 'pending';
        $direction = Message::OUTGOING;
        $query = $this->selectQuery(compact('status', 'direction'))
            ->limit($limit)
            ->order_by('created', 'ASC')
            // Include contact in same query
            ->join('contacts', 'LEFT')->on('contacts.id', '=', 'messages.contact_id')
            ->select('contacts.contact')
            ->select(['contacts.type', 'contact_type'])
            // Only return messages without a specified provider
            ->where('messages.data_source', 'IS', null)
            ;

        if ($type) {
            $query->where('messages.type', '=', $type);
        }

        $results = $query->execute($this->db());

        return $this->getCollection($results->as_array());
    }

    // MessageRepository
    public function updateMessageStatus($id, $status, $data_source_message_id = null)
    {
        $changes = [
            'status'   => $status,
            'data_source_message_id' => $data_source_message_id
        ];

        return $this->executeUpdate(['id' => $id], $changes);
    }

    public function getTotalMessagesFromContact($contact_id)
    {
        $direction = Message::INCOMING;
        return (int) $this->selectCount(compact('contact_id', 'direction'));
    }

    // CreateRepository
    public function create(Entity $entity)
    {

        $message = $entity->asArray();
        // Set default value for post_date
        if (!empty($message['datetime'])) {
            $message['datetime'] = $message['datetime']->format("Y-m-d H:i:s");
        }
        $message['created'] = time();
        // Unset related properties
        unset($message['contact_type']);
        // Create the post
        return $this->executeInsert($this->removeNullValues($message));
    }

    public function createMany(Collection $collection) : array
    {
        $this->checkAutoIncMode();

        $first = $collection->first()->asArray();
        // Unset related properties
        unset($first['contact']);
        unset($first['contact_type']);
        $columns = array_keys($first);

        $values = $collection->map(function ($entity) {
            $data = $entity->asArray();
            // Unset related properties
            unset($data['contact']);
            unset($data['contact_type']);

            // Format date value
            if (!empty($data['datetime'])) {
                $data['datetime'] = $data['datetime']->format("Y-m-d H:i:s");
            }
            $data['created'] = time();


            // JSON encode values
            $data = $this->json_transcoder->encode(
                $data,
                $this->getJsonProperties()
            );

            return $data;
        })->all();

        $query = DB::insert($this->getTable())
            ->columns($columns);

        call_user_func_array([$query, 'values'], $values);

        list($insertId, $created) = $query->execute($this->db());

        return range($insertId, $insertId + $created - 1);
    }

    // Update Repository
    public function update(Entity $entity)
    {

        $message = $entity->asArray();
        // Set default value for post_date
        if (!empty($message['datetime'])) {
            $message['datetime'] = $message['datetime']->format("Y-m-d H:i:s");
        }
        // Unset related properties
        unset($message['contact_type']);
        // Create the post
        return $this->executeUpdate(['id' => $message['id']], $this->removeNullValues($message));
    }

    // UpdateMessageRepository
    public function checkStatus($status, $direction)
    {
        if ($direction === MessageDirection::INCOMING) {
            return ($status == MessageStatus::RECEIVED);
        }

        if ($direction === MessageDirection::OUTGOING) {
            // Outgoing messages can only be: pending, cancelled, failed, unknown, sent
            return in_array($status, [
                MessageStatus::PENDING,
                MessageStatus::EXPIRED,
                MessageStatus::CANCELLED,
            ]);
        }

        return false;
    }

    public function getLastUID($data_source)
    {
        $last_uid = null;
        $query = DB::select([DB::expr('ABS(' . $this->getTable() . '.' . 'data_source_message_id' . ')'), 'uid'])
            ->from($this->getTable())
            ->where('data_source', '=', $data_source)
            ->order_by(
                'uid',
                'desc'
            )
            ->limit(1);
        $result =   $query->execute($this->db());

        $last_uid = $result->get('uid', 0) ? $result->get('uid', 0) : null;

        return $last_uid;
    }

    /**
     * For CreateMessageRepository
     * @param  int $parent_id
     * @return Boolean
     */
    public function parentExists($parent_id)
    {
        return $this->selectCount(['id' => $parent_id]) > 0;
    }

    //MessageRepository
    public function notificationMessageExists($post_id, $contact_id)
    {
        return $this->selectCount(
            ['notification_post_id' => $post_id, 'contact_id' => $contact_id, 'direction' => 'outgoing']
        ) > 0;
    }
}
