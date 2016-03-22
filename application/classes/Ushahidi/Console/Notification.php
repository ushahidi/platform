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

		$site_name = Kohana::$config->load('site.name') ?: 'Ushahidi';
		$client_url = Kohana::$config->load('site.client_url');

		// Get contacts (max $limit at a time) and generate messages.
		while (TRUE) {
			$contacts = $this->contactRepository
				->getNotificationContacts($notification->set_id, $limit, $offset);

			// Create outgoing messages
			foreach ($contacts as $contact) {
				if ($this->messageRepository->notificationMessageExists($post->id, $contact->id)) {
					continue;
				}

				$subs = [
					':sitename' => $site_name,
					':title' => $post->title,
					':content' => $post->content,
					':url' => $client_url . '/posts/' . $post->id
				];

				$messageType = $this->mapContactToMessageType($contact->type);
				$data_provider = $contact->data_provider ?: \DataProvider::getProviderForType($messageType);

				$state = [
					'contact_id' => $contact->id,
					'notification_post_id' => $post->id,
					'title' => strtr(Kohana::message('notifications', $messageType . '.title', "New post: :title"), $subs),
					'message' => strtr(Kohana::message('notifications',  $messageType . '.message', "New post: :title"), $subs),
					'type' => $messageType,
					'data_provider' => $data_provider,
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


	private $contactToMessageTypeMap = [
		'phone' => 'sms',
		'email' => 'email',
		'twitter' => 'twitter',
	];

	private function mapContactToMessageType($contactType)
	{
		return isset($this->contactToMessageTypeMap[$contactType]) ? $this->contactToMessageTypeMap[$contactType] : $contactType;
	}
}
