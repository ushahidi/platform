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

use Ushahidi\Factory\DataFactory;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_SavedSearch extends Command
{
	private $contactRepository;
	private $setRepository;
	private $postRepository;
	private $messageRepository;
	private $data;
	private $postSearchData;

	public function setDataFactory(DataFactory $data)
	{
		$this->data = $data;
	}

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
			->setName('savedsearch')
			->setDescription('Search and add posts to Saved Searches')
			;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$count = 0;

		// Get saved searches
		$this->setRepository->setSearchParams($this->data->get('search'));

		// @todo Might need to limit the number of saved searches retrieved at a time
		$savedSearches = $this->setRepository->getSearchResults();

		foreach ($savedSearches as $savedSearch) {
			// Get fresh SearchData
			$data = $this->data->get('search');

			// Get posts with the search filter
			foreach ($savedSearch->filter as $key => $filter) {
				$data->$key = $filter;
			}

			$this->postRepository->setSearchParams($data);
			$posts = $this->postRepository->getSearchResults();

			foreach ($posts as $post) {
				if (! $this->setRepository->setPostExists($savedSearch->id, $post->id)) {
					$this->setRepository->addPostToSet($savedSearch->id, $post->id);
					$count++;
				}
			}
		}

		$response = [
			[
				'Message' => sprintf('%d posts were added', $count)
			]
		];

		$this->handleResponse($response, $output);
	}
}
