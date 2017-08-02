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
        // Get the user ID
        $user = service('session.user');
        $user_id = $user->id;

        // Save the agreement date to the current time and the user ID
        $state = [
            'agreement_date'  => time(),
            'user_id'         => $user_id,
        ];

        return parent::create($entity->setState($state));
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
