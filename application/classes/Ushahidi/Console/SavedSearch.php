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

use Ushahidi\Core\Entity\NotificationQueueRepository;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\MessageRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_SavedSearch extends Command
{
	private $notificationQueueRepository;
	private $setRepository;
	private $postRepository;
	private $messageRepository;
	private $db;

	public function setNotificationQueueRepo(NotificationQueueRepository $repo)
	{
		$this->notificationQueueRepository = $repo;
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
			->setName('savedsearch')
			->setDescription('Queue new posts from saved searches')
			->addArgument('action', InputArgument::OPTIONAL, 'list, queue', 'list')
			->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'number of notifications to queue per search')
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

		// XXX: Need to figure out a good allowed limit for new posts so that subscribers
		// are not flooded with notifications when this is run
		$limit = $input->getOption('limit') ?: 5; // Default to 5 posts per search

		// Search data
		$search = service('factory.data')->get('search');
		
		$this->db = service('kohana.db');

		// Get saved searches
		$this->setRepository->setSearchParams($search);

		$savedSearches = $this->setRepository->getSearchResults();

		// @todo Might need to limit the number of saved searches
		foreach ($savedSearches as $savedSearch) {
			// Get posts with the search filter
			$search = service('factory.data')->get('search');
			$search->q = $savedSearch->filter['q'];
			$search->orderby = 'id';
			$search->order = 'DESC';
			$search->limit = $limit;
			$this->postRepository->setSearchParams($search);
			$posts = $this->postRepository->getSearchResults();

			foreach ($posts as $post) {
				// No need to proceed beyond ids that was saved before
				if ($this->_exists($post->id, $savedSearch->id)) {
					break;
				}
				
				$this->db->begin();
				
				$state = [
					'set'  => $savedSearch->id,
					'post' => $post->id
				];

				$entity = $this->notificationQueueRepository->getEntity();
				$entity->setState($state);
				$this->notificationQueueRepository->create($entity);

				// save post and saved search id
				$this->_save($post->id, $savedSearch->id);
				$this->db->commit();
				
				$count++;
			}
		}

		return [
			[
				'Message' => sprintf('%d notification(s) queued', $count)
			]
		];
	}

	private function _exists($post_id, $set_id)
	{
		$result = DB::select([DB::expr('COUNT(*)'), 'total'])
			->from('posts_savedsearches')
			->where('set_id', '=', $set_id)
			->and_where('post_id', '=', $post_id)
			->execute($this->db);

		return (bool) $result->get('total');
	}

	private function _save($post_id, $set_id)
	{
		$result = DB::insert('posts_savedsearches', ['post_id', 'set_id'])
			->values([$post_id, $set_id])
			->execute($this->db);
	}
}
