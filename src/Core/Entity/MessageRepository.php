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

interface MessageRepository extends
	EntityGet,
	EntityExists
{

	/**
	 * Load pending message by data provider and status (pending or pending_poll)
	 *
	 * @param  String $status
	 * @param  String $data_provider
	 * @param  integer $limit
	 * @return [Message, ...]
	 */
	public function getPendingMessages($status, $data_provider, $limit);

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
}
