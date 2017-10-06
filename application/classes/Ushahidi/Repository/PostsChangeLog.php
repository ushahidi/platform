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

class Ushahidi_Repository_PostsChangeLog extends Ushahidi_Repository implements PostsChangeLogRepository
{
    use UserContext;

    // Ushahidi_Repository
    protected function getTable()
    {
        return 'postschangelog';
    }

    public function getSearchFields()
  	{
      return ['post_id', 'entry_id'];
    }

    // CreateRepository
    public function create(Entity $entity)
    {
      $user = $this->getUser();
      \Log::instance()->add(\Log::INFO, 'Here is the gotten user:'.print_r($user->getId(), true));

        $data = $entity->asArray();

        \Log::instance()->add(\Log::INFO, 'Here is the data as array:'.print_r($entity->asArray(), true));


        $data['created']  = time();
        $data['user_id'] = $this->getUserId();

        \Log::instance()->add(\Log::INFO, 'New PostsChangeLog data: '.print_r($data, true));

        return $this->executeInsert($this->removeNullValues($data));
    }

    public function getEntity(Array $data = null)
    {
        return new PostsChangeLog($data);
    }

    // Overriding so we can alter sorting logic
    // @todo make it easier to override just sorting
    public function setSearchParams(SearchData $search)
    {

      $this->search_query = $this->selectQuery();

      $sorting = $search->getSorting();

      $this->search_query->order_by('created', 'DESC');

      //QUESTION -- these are just reassembling the existing pieces back to sorting??
      if (!empty($sorting['orderby'])) {
        $this->search_query->order_by(
          $this->getTable() . '.' . $sorting['orderby'],
          Arr::get($sorting, 'order')
        );
      }

      if (!empty($sorting['offset'])) {
        $this->search_query->offset($sorting['offset']);
      }

      if (!empty($sorting['limit'])) {
        $this->search_query->limit($sorting['limit']);
      }
    }


    public function getSearchResults()
    {
        $query = $this->getSearchQuery();

        $results = $query->distinct(TRUE)->execute($this->db);
        return $this->getCollection($results->as_array());
    }

}
