<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Saved Search Console Command
 * Discover and queue new posts from Saved Searches
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;

use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\MessageRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_Notification_SavedSearch extends Command
{
	private $contactRepository;
	private $setRepository;
	private $postRepository;
	private $messageRepository;
	private $db;

	public function setContactRepo(ContactRepository $repo)
	{
		$this->contactRepository = $repo;
	}

	public function setSetRepo(SetRepository $repo)
	{
		$this->setRepository = $repo;
	}

	public function setPostRepo(PostRepository $repo)
	{
		$this->postRepository = $repo;
	}

	public function setMessageRepo(MessageRepository $repo)
	{
		$this->messageRepository = $repo;
	}

	protected function configure()
	{
		$this
			->setName('notification:savedsearch')
			->setDescription('Manage notifications for Saved Searches')
			->addArgument('action', InputArgument::OPTIONAL, 'list, queue', 'list')
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
		$count = 0;

		// Search data
		$search = service('factory.data')->get('search');
		
		$this->db = service('kohana.db');

		// Get saved searches
		$this->setRepository->setSearchParams($search);

		// @todo Might need to limit the number of saved searches retrieved at a time
		$savedSearches = $this->setRepository->getSearchResults();

		$config = Kohana::$config->load('saved_search');
		$lastSearchTimestamp = $config->get('last_search_timestamp') ?: 0;
		$lastSearchTimestamp = date('Y-m-d H:i:s', $lastSearchTimestamp);

		// Start transaction
		$this->db->begin();

		foreach ($savedSearches as $savedSearch) {
			// Get posts with the search filter
			$search = service('factory.data')->get('search');
			$search->q = $savedSearch->filter['q'];
			$search->created_after = $lastSearchTimestamp;
			$search->orderby = 'id';
			$search->order = 'DESC';

			$this->postRepository->setSearchParams($search);
			$postCount = $this->postRepository->getSearchTotal();

			if ($postCount > 0) {
				$count+=$this->generateMessages($savedSearch, $postCount);
			}
		}

		// Save latest post timestamp to use when we search for posts next time.
		$config->set('last_search_timestamp', $this->getLatestPostTimestamp());

		// Finally commit changes
		$this->db->commit();

		return [
			[
				'Message' => sprintf('%d messages queued for sending', $count)
			]
		];
	}

	private function generateMessages($savedSearch, $postCount)
	{
		$count = 0;
		
		$offset = 0;
		$limit = 1000;

		$title = $savedSearch->name;
		
		// @todo translate this message
		$message = sprintf('There are %d new posts in your saved search %s', $postCount, $title);

		while (TRUE) {
			$contacts = $this->contactRepository
				->getNotificationContacts($savedSearch->id, $limit, $offset);
			
			// Create outgoing messages
			foreach ($contacts as $contact) {
				$state = [
					'contact_id' => $contact->id,
					'title' => $title,
					'message' => $message,
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

	private function getLatestPostTimestamp()
	{
		$search = service('factory.data')->get('search');
		
		$search->orderby = 'created';
		$search->order = 'DESC';
		$search->limit = 1;

		$this->postRepository->setSearchParams($search);
		$posts = $this->postRepository->getSearchResults();

		$post = array_shift($posts);

		// exclude timestamp incase it was pulled in during this round of searches
		return $post->created + 1;
	}
}
