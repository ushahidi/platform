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
use Ushahidi\Core\Entity\Tos;
use Ushahidi\Core\Entity\TosRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Traits\UserContext;



class Ushahidi_Repository_Tos extends Ushahidi_Repository implements
    TosRepository
{
    use UserContext;


    // Ushahidi_Repository
    protected function getTable()
    {
        return 'tos';
    }


    // CreateRepository
    public function create(Entity $entity)
    {
        $data = $entity->asArray();

        // Save the agreement date to the current time and the user ID
        $data['agreement_date']  = time();
        // Convert tos_version_date to timestamp
        $data['tos_version_date'] = $data['tos_version_date']->format("U");

        return $this->executeInsert($this->removeNullValues($data));
    }

    public function getEntity(Array $data = null)
    {
        return new Tos($data);
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
