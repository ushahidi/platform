<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tos Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostsChangeLog;
use Ushahidi\Core\Entity\PostsChangeLogRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Traits\UserContext;



class Ushahidi_Repository_PostsChangeLog extends Ushahidi_Repository implements
    PostsChangeLogRepository
{
    use UserContext;


    // Ushahidi_Repository
    protected function getTable()
    {
        return 'postschangelog';
    }


    // CreateRepository
    public function create(Entity $entity)
    {
        $data = $entity->asArray();

        // Save the agreement date to the current time and the user ID
        $data['changelog_ts']  = time();
        $data['changed_by_user_id'] = $this->getUserId();
        // Convert tos_version_date to timestamp

        return $this->executeInsert($this->removeNullValues($data));
    }

    public function getEntity(Array $data = null)
    {
        return new PostsChangeLog($data);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return [];
    }

    protected function setSearchConditions(SearchData $search)
    {

        $query = $this->search_query;

        $query->where('user_id', '=', $this->getUserId());
    }

    public function getSearchResults()
    {
        $query = $this->getSearchQuery();
        $results = $query->distinct(TRUE)->execute($this->db);
        return $this->getCollection($results->as_array());
    }

}
