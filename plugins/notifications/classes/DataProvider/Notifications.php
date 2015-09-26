<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Notifications Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Notifications
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class DataProvider_Notifications extends DataProvider
{
	private $db;
	private $postRepository;
	private $messageRepository;
	private $notificationQueueRepository;

	public function fetch($limit = FALSE)
	{
		// Get database and repos
		$this->db = service('kohana.db');
		$this->postRepository = service('repository.post');
		$this->messageRepository = service('repository.message');
		$this->notificationQueueRepository = service('repository.notification.queue');

		$count = 0;

		// Get Queued notifications
		$notifications = $this->notificationQueueRepository->getNotifications($limit);

		// Start transaction
		$this->db->begin();

		foreach ($notifications as $notification) {
			// Get contacts and generate messages from new notification
			$count+=$this->_generate_messages($notification);
		}

		// Finally commit changes
		$this->db->commit();

		return $count;
	}

	private function _generate_messages($notification)
	{
		// Delete queued notification
		$this->notificationQueueRepository->delete($notification);

		// Get post title and text
		$post = $this->postRepository->get($notification->post_id);

		$count = 0;

		$offset = 0;
		$limit = 1000;

		// Get contacts (max $limit at a time) and generate messages.
		while (TRUE) {
			$contacts = DB::select('contacts.id', 'contacts.type')
				->distinct(TRUE)
				->from('contacts')
				->limit($limit)
				->offset($offset)
				->join('notifications')
				->on('contacts.user_id', '=', 'notifications.user_id')
				->where('set_id', '=', $notification->set_id)
				->and_where('contacts.can_notify', '=', '1')
				->execute($this->db)
				->as_array();

			// Create outgoing messages
			foreach ($contacts as $contact) {
				$state = [
					'contact_id' => $contact['id'],
					'post_id' => $post->id,
					'title' => $post->title,
					'message' => $post->content,
					'type' => $contact['type']
				];

				$entity = $this->messageRepository->getEntity();
				$entity->setState($state);
				$this->messageRepository->create($entity);

				$count++;
			}

			if (count($contacts) < $limit) {
				break;
			}

			$offset+=$limit;
		}

		return $count;
	}
}
