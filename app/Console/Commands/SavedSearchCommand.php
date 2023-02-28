<?php

/**
 * Ushahidi Saved Search Console Command
 * Discover and queue new posts from Saved Searches
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Tool\SearchData;

class SavedSearchCommand extends Command
{
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

    public function handle(PostRepository $postRepo, SetRepository $setRepo, SearchData $data)
    {
        $count = 0;

        $setRepo->setSavedSearch(true);

        // Get saved searches
        $setRepo->setSearchParams($data);

        // @todo Might need to limit the number of saved searches retrieved at a time
        $savedSearches = $setRepo->getSearchResults();

        foreach ($savedSearches as $savedSearch) {
            // Get fresh SearchData

            // Get posts with the search filter
            foreach ($savedSearch->filter as $key => $filter) {
                $data->$key = $filter;
            }

            $postRepo->setSearchParams($data);
            $posts = $postRepo->getSearchResults();

            foreach ($posts as $post) {
                if (! $setRepo->setPostExists($savedSearch->id, $post->id)) {
                    $setRepo->addPostToSet($savedSearch->id, $post->id);
                    $count++;
                }
            }
        }

        $this->info("{$count} posts were added");
    }
}
