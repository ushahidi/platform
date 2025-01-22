<?php

/**
 * Ushahidi Tos Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository;

use Ohanzee\DB;
use Ushahidi\Core\Entity\Tos;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Contracts\Repository\Entity\TosRepository as TosRepositoryContract;

class TosRepository extends OhanzeeRepository implements
    TosRepositoryContract
{
    use UserContext;


    // OhanzeeRepository
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

    public function getEntity(array $data = null)
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
        $results = $query->distinct(true)->execute($this->db());
        return $this->getCollection($results->as_array());
    }
}
