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
use Ushahidi\Core\Entity\ContactRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_Notification extends Command
{
	private $db;
	private $postRepository;
	private $contactRepository;
	private $messageRepository;
	private $notificationQueueRepository;

	public function setDatabase(Database $db)
	{
		$this->db = $db;
	}

	public function setContactRepo(ContactRepository $repo)
	{
		$this->contactRepository = $repo;
	}

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
		
		$count = 0;

		// Get Queued notifications
		$notifications = $this->notificationQueueRepository->getNotifications($limit);

		// Start transaction
		$this->db->begin();

		foreach ($notifications as $notification) {
			// Get contacts and generate messages from new notification
			$count+=$this->generateMessages($notification);
		}

		// Finally commit changes
		$this->db->commit();

		return [
			[
				'Message' => sprintf('%d messages queued for sending', $count)
			]
		];
	}

	private function generateMessages($notification)
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
			$contacts = $this->contactRepository
				->getNotificationContacts($notification->set_id, $limit, $offset);
			
			// Create outgoing messages
			foreach ($contacts as $contact) {
				$state = [
					'contact_id' => $contact->id,
					'post_id' => $post->id,
					'title' => $post->title,
					'message' => $post->content,
					'type' => $contact->type
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
