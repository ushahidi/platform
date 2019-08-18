<?php

/**
 * Ushahidi Saved Search Console Command
 * Discover and queue new posts from Saved Searches
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;

use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Factory\DataFactory;

class SavedSearch extends Command
{
    private $contactRepository;
    private $setRepository;
    private $postRepository;
    private $messageRepository;
    private $data;
    private $postSearchData;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'savedsearch:sync';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'savedsearch:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync saved search posts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->data = service('factory.data');
        $this->contactRepository = service('repository.contact');
        $this->setRepository = service('repository.savedsearch');
        $this->postRepository = service('repository.post');
        $this->messageRepository = service('repository.message');

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

        $this->info("{$count} posts were added");
    }
}
