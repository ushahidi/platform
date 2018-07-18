<?php

/**
 * Repository for Message
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;
use Ushahidi\Core\Usecase\CreateRepository;
use Ushahidi\Core\Usecase\UpdateRepository;

interface MessageRepository extends
    EntityGet,
    EntityExists,
    CreateRepository,
    UpdateRepository
{

    /**
     * Load pending message by data provider
     *
     * @param  String $status
     * @param  String $data_source
     * @param  integer $limit
     * @return [Message, ...]
     */
    public function getPendingMessages($data_source, $limit);

    /**
     * Load pending message by type
     *
     * @param  String $status
     * @param  String $data_source
     * @param  integer $limit
     * @return [Message, ...]
     */
    public function getPendingMessagesByType($type, $limit);

    /**
     * Check whether a notification message has been sent to a contact
     *
     * @param int $post_id
     * @param int $contact_id
     * @return bool
     */
    public function notificationMessageExists($post_id, $contact_id);

    /**
     * Get number of messages sent by the given contact
     * @return int
     */
    public function getTotalMessagesFromContact($contact_id);

    /**
     * Update message status
     * @param  int    $id
     * @param  string $status
     * @param  string $data_source_message_id
     * @return null
     */
    public function updateMessageStatus($id, $status, $data_source_message_id = null);

    /**
     * Get most recent message UID
     * @param  string $data_source
     * @return string
     */
    public function getLastUID($data_source);
}
