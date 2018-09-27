<?php

/**
 * Ushahidi Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ohanzee\DB;
use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\ConfidenceScore;
use Ushahidi\Core\Entity\ConfidenceScoreRepository as ConfidenceScoreRepositoryContract;
use Ushahidi\Core\Usecase\Post\Ushahidi;
use Ushahidi\Core\Usecase\ConfidenceScore\UpdateConfidenceScoreRepository;

class ConfidenceScoreRepository extends OhanzeeRepository implements
    UpdateConfidenceScoreRepository,
    ConfidenceScoreRepositoryContract
{
    // Use trait to for updating forms_tags-table
    use FormsTagsTrait;
    // OhanzeeRepository
    protected function getTable()
    {
        return 'confidence_scores';
    }

    // CreateRepository
    // ReadRepository
    public function getEntity(array $data = null)
    {
        return new ConfidenceScore($data);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['post_tag_id' /* LIKE tag */];
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        foreach (['post_tag_id'] as $key) {
            if ($search->$key) {
                $query->where($key, '=', $search->$key);
            }
        }
    }

    // SearchRepository
    public function getSearchResults()
    {
        $query = $this->getSearchQuery();
        $results = $query->distinct(true)->execute($this->db);
        return $this->getCollection($results->as_array());
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $record = $entity->asArray();
        $id = $this->executeInsert($this->removeNullValues($record));
        return $id;
    }
    public function update(Entity $entity)
    {
        $count = $this->executeUpdate(['id' => $entity->id], $entity->asArray());
        return $count;
    }

    // UpdatePostTagRepository
    public function getByTag($tag)
    {
        return $this->getEntity($this->selectOne(compact('tag')));
    }


    // UpdatePostTagRepository
    public function getByPost($post_id)
    {
        $query = parent::selectQuery(['posts_tags.post_id' => $post_id]);

        // Select 'key' too
        $query->select(
            $this->getTable().'.*',
            'posts_tags.tag_id'
        )
            ->join('posts_tags')
            ->on('posts_tags.id', '=', 'confidence_scores.post_tag_id');
        return $query->execute($this->db);
    }

    public function delete(Entity $entity)
    {
        // Remove tag from attribute options
        $this->removeTagFromAttributeOptions($entity->id);

        return $this->executeDelete([
            'id' => $entity->id
        ]);
    }

    /**
     * @param $post_tag
     * @return \Ushahidi\Core\Entity|ConfidenceScore|Ushahidi\Core\Entity\Tag
     */
    public function getByPostTag($post_tag_id)
    {
        return $this->getEntity($this->selectOne(compact('post_tag_id')));
    }

    /**
     * @param  string $tag
     * @return Boolean
     */
    public function doesPostTagExist($post_tag_or_id)
    {
        // TODO: Implement doesPostTagExist() method.
    }
}
