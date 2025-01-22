<?php

/**
 * Ushahidi Set Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository;

use Ohanzee\DB;
use Ushahidi\Core\Entity\Set;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Concerns\Event;
use Ushahidi\Core\Entity\SavedSearch;
use Ushahidi\Contracts\Repository\Entity\SetRepository as SetRepositoryContract;
use Ushahidi\Contracts\Search;

class SetRepository extends OhanzeeRepository implements SetRepositoryContract
{
    // Use the JSON transcoder to encode properties
    use Concerns\JsonTranscode;

    // Use Event trait to trigger events
    use Event;

    /**
     * @var  Boolean  Return SavedSearches (when true) or vanilla Sets
     **/
    protected $savedSearch = false;

    protected $listener;

    public function setSavedSearch($savedSearch)
    {
        $this->savedSearch = $savedSearch;
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'sets';
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        return $this->savedSearch ? new SavedSearch($data) : new Set($data);
    }

    // Concerns\JsonTranscode
    protected function getJsonProperties()
    {
        return ['filter', 'view_options', 'role'];
    }

    /**
     * Override selectQuery to enforce filtering by search=0/1
     */
    protected function selectQuery(array $where = [])
    {
        $query = parent::selectQuery($where);

        $query->where('search', '=', (int)$this->savedSearch);

        return $query;
    }

    /*
     * Override core get/create/update/delete methods to only include
     * saved searches or sets depending on $this->savedSearch
     */

    // CreateRepository
    public function create(Entity $entity)
    {
        // Get record and filter empty values
        $record = array_filter($entity->asArray());

        // Set the created time
        $record['created'] = time();

        // And save if this is a saved search or collection
        $record['search'] = (int)$this->savedSearch;

        // Finally, save the record to the DB
        return $this->executeInsert($this->removeNullValues($record));
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        // Get changed values
        $record = $entity->getChanged();

        // Set the updated time
        $record['updated'] = time();

        // Finally, update the record in the DB
        return $this->executeUpdate([
            'id' => $entity->id,
            'search' => (int)$this->savedSearch
        ], $record);
    }

    // DeleteRepository
    public function delete(Entity $entity)
    {
        return $this->executeDelete([
            'id' => $entity->id,
            'search' => (int)$this->savedSearch
        ]);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return [
            'user_id',
            'q', /* LIKE name */
            'featured',
        ];
    }

    // SearchRepository
    public function setSearchParams(Search $search)
    {
        // Overriding so we can alter sorting logic
        // @todo make it easier to override just sorting

        $this->search_query = $this->selectQuery();

        $sorting = $search->getSorting();

        // Always return featured sets first
        // @todo make this optional
        $this->search_query->order_by('sets.featured', 'DESC');

        if (!empty($sorting['orderby'])) {
            $this->search_query->order_by(
                $this->getTable() . '.' . $sorting['orderby'],
                isset($sorting['order']) ? $sorting['order'] : null
            );
        }

        if (!empty($sorting['offset'])) {
            $this->search_query->offset($sorting['offset']);
        }

        if (!empty($sorting['limit'])) {
            $this->search_query->limit($sorting['limit']);
        }

        // apply the unique conditions of the search
        $this->setSearchConditions($search);
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
    {
        $sets_query = $this->search_query;

        if ($search->q) {
            $sets_query->where('name', 'LIKE', "%{$search->q}%");
        }

        if ($search->featured !== null) {
            $sets_query->where('featured', '=', (int)$search->featured);
        }

        if ($search->user_id) {
            $sets_query->where('user_id', '=', $search->user_id);
        }

        if (isset($search->search)) {
            $sets_query->where('search', '=', (int)$search->search);
        }

        if ($search->id) {
            $sets_query->where('id', '=', $search->id);
        }
    }

    // SetRepository
    public function deleteSetPost($set_id, $post_id)
    {
        DB::delete('posts_sets')
            ->where('post_id', '=', $post_id)
            ->where('set_id', '=', $set_id)
            ->execute($this->db());
    }

    // SetRepository
    public function setPostExists($set_id, $post_id)
    {
        $result = DB::select('posts_sets.*')
            ->from('posts_sets')
            ->where('post_id', '=', $post_id)
            ->where('set_id', '=', $set_id)
            ->execute($this->db())
            ->as_array();

        return (bool) count($result);
    }

    // SetRepository
    public function addPostToSet($set_id, $post_id)
    {
        // Ensure post_id is an int
        // @todo this probably should have happened elsewhere
        $post_id = (int)$post_id;
        $set_id = (int)$set_id;

        DB::insert('posts_sets')
            ->columns(['post_id', 'set_id'])
            ->values(array_values(compact('post_id', 'set_id')))
            ->execute($this->db());

        // Fire event after post is added
        // so that this is queued for the Notifications data provider
        $this->emit($this->event, $set_id, $post_id);
    }

    /**
     * Gets the set names corresponding to the list of tag ids
     * @param $tag_ids
     * @return array
     */
    public function getNamesByIds($sets_ids)
    {
        $result = $this->selectQuery(['id' => $sets_ids])
            ->resetSelect()
            ->select('name')
            ->execute($this->db());
        $result = $result->as_array(null, 'name');
        return $result;
    }
}
