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


class Ushahidi_Repository_Tos extends Ushahidi_Repository implements
    TosRepository
{

    // Ushahidi_Repository
    protected function getTable()
    {
        return 'tos';
    }

    // SearchRepository
    public function getSearchFields()
    {
        return [
            'user_id'
        ];
    }

    public function getEntity(Array $data = null)
    {

        return new Tos($data);
    }


    protected function setSearchConditions(SearchData $search)
    {

        $query = $this->search_query;
        foreach (['user_id'] as $key)
        {
            if ($search->$key) {
                 $query->where($key, '=', $search->$key);
            }
        }
    }

    public function getSearchResults()
    {
        $query = $this->getSearchQuery();
        $results = $query->distinct(TRUE)->execute($this->db);
        return $this->getCollection($results->as_array());
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        //get the user ID
        $user = service('session.user');
        $user_id = $user->id;

        //save the agreement date to the current time 
        //and the user ID
        $state = [
            'agreement_date'  => time(),
            'user_id'         => $user_id,
        ];

        return parent::create($entity->setState($state));
    }

}
