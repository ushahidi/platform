<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Notifications Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;

use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\NotificationQueueRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_Notification extends Command
{
	private $db;
	private $postRepository;
	private $messageRepository;
	private $notificationQueueRepository;

	public function setPostRepo(PostRepository $repo)
	{
		$this->postRepository = $repo;
	}

	public function setMessageRepo(MessageRepository $repo)
	{
		$this->messageRepository = $repo;
	}

	public function setNotificationQueueRepo(NotificationQueueRepository $repo)
	{
		$this->notificationQueueRepository = $repo;
	}

	protected function configure()
	{
		$this
			->setName('notification')
			->setDescription('Manage notifications')
			->addArgument('action', InputArgument::OPTIONAL, 'list, queue', 'list')
			->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'number of notifications')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		return [
			[
				'Available actions' => 'queue'
			]
		];
	}

	protected function executeQueue(InputInterface $input, OutputInterface $output)
	{
		$limit = $input->getOption('limit');
		
		// Get database and repos
		$this->db = service('kohana.db');

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

		return [
			[
				'Message' => sprintf('%d messages queued for sending', $count)
			]
		];
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
