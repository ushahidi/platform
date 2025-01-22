<?php

/**
 * Ushahidi Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository;

use Ohanzee\DB;
use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Tag;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\ValidationEngine;
use Ushahidi\Modules\V3\Repository\OhanzeeRepository;
use Ushahidi\Modules\V3\Repository\Concerns;
use Ushahidi\Contracts\Repository\Usecase\DeleteTagRepository;
use Ushahidi\Contracts\Repository\Usecase\UpdateTagRepository;
use Ushahidi\Contracts\Repository\Usecase\UpdatePostTagRepository;
use Ushahidi\Contracts\Repository\Entity\TagRepository as TagRepositoryContract;

class TagRepository extends OhanzeeRepository implements
    UpdateTagRepository,
    DeleteTagRepository,
    UpdatePostTagRepository,
    TagRepositoryContract
{
    // Use the JSON transcoder to encode properties
    use Concerns\JsonTranscode;
    // Use trait to for updating forms_tags-table
    use Concerns\FormsTags;
    use Concerns\UsesBulkAutoIncrement;

    private $created_id;

    private $created_ts;

    private $deleted_tag;

    // OhanzeeRepository
    protected function getTable()
    {
        return 'tags';
    }

    // CreateRepository
    // ReadRepository
    public function getEntity(array $data = null)
    {
        if (!empty($data['id'])) {
            // If this is a top level category
            if (empty($data['parent_id'])) {
                // Load children
                $data['children'] = DB::select('id')
                    ->from('tags')
                    ->where('parent_id', '=', $data['id'])
                    ->execute($this->db())
                    ->as_array(null, 'id');
            }
        }

        return new Tag($data);
    }

    // Concerns\JsonTranscode
    protected function getJsonProperties()
    {
        return ['role'];
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['tag', 'type', 'parent_id', 'q', 'level' /* LIKE tag */];
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        foreach (['tag', 'type', 'parent_id'] as $key) {
            if ($search->$key) {
                $query->where($key, '=', $search->$key);
            }
        }

        if ($search->q) {
            // Tag text searching
            $query->where('tag', 'LIKE', "%{$search->q}%");
        }

        if ($search->level) {
            // searching for top-level-tags
            if ($search->level === 'parent') {
                $query->where('parent_id', '=', null);
            }
        }
    }

    // SearchRepository
    public function getSearchResults()
    {
        $query = $this->getSearchQuery();
        $results = $query->distinct(true)->execute($this->db());
        return $this->getCollection($results->as_array());
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $record = $entity->asArray();
        $record['created'] = time();

        $id = $this->executeInsert($this->removeNullValues($record));

        return $id;
    }

    public function createMany(Collection $collection) : array
    {
        $this->checkAutoIncMode();

        $first = $collection->first()->asArray();
        unset($first['children']);
        $columns = array_keys($first);

        $values = $collection->map(function ($entity) {
            $data = $entity->asArray();

            unset($data['children']);
            $data['created'] = time();

            // JSON encode values
            $data = $this->json_transcoder->encode(
                $data,
                $this->getJsonProperties()
            );

            return $data;
        })->all();

        $query = DB::insert($this->getTable())
            ->columns($columns);

        call_user_func_array([$query, 'values'], $values);

        list($insertId, $created) = $query->execute($this->db());

        return range($insertId, $insertId + $created - 1);
    }

    public function update(Entity $entity)
    {
        $tag = $entity->getChanged();
        // removing children before saving tag
        unset($tag['children']);
        $count = $this->executeUpdate(['id' => $entity->id], $tag);

        return $count;
    }

    // UpdatePostTagRepository
    public function getByTag($tag)
    {
        return $this->getEntity($this->selectOne(compact('tag')));
    }

    // UpdatePostTagRepository
    public function doesTagExist($tag_or_id)
    {
        $query = $this->selectQuery()
            ->resetSelect()
            ->select([DB::expr('COUNT(*)'), 'total'])
            ->where('id', '=', $tag_or_id)
            ->or_where('tag', '=', $tag_or_id)
            ->execute($this->db());

        return $query->get('total') > 0;
    }

    // UpdateTagRepository
    public function isSlugAvailable($slug)
    {
        return $this->selectCount(compact('slug')) === 0;
    }

    public function delete(Entity $entity)
    {
        // Remove tag from attribute options
        $this->removeTagFromAttributeOptions($entity->id);

        return $this->executeDelete([
            'id' => $entity->id
        ]);
    }

    // DeleteTagRepository
    public function deleteTag($id)
    {
        // Remove tag from attribute options
        $this->removeTagFromAttributeOptions($id);
        return $this->delete(compact('id'));
    }

    /**
     * Checks if the assigned role is valid for this tag.
     * True if there is no role or if it's a parent with no children
     * @return bool
     */
    public function isRoleValid(ValidationEngine $validation, $tag)
    {
        $isChild = !!$tag['parent_id'];
        $parent = $isChild ? $this->selectOne(['id' => $tag['parent_id']]) : null;

        // If tag has a role and is a child category
        if ($isChild && $parent) {
            // ... load the parent
            $parent = $this->getEntity($parent);

            // ... and check if the role matches its parent
            if ($parent->role != $tag['role']) {
                // If it doesn't, set a validation error
                // We have to do this here because an empty field gets ignored
                // by KohanaValidation
                $validation->error('role', 'isRoleValid');
                // And return false
                return false;
            }
        }

        // Otherwise role is fine
        return true;
    }

    /**
     * Gets the tag names corresponding to the list of tag ids
     * @param $tag_ids
     * @return array
     */
    public function getNamesByIds($tag_ids)
    {
        $result = $this->selectQuery(['id' => $tag_ids])
            ->resetSelect()
            ->select('tag')
            ->execute($this->db());
        $result = $result->as_array(null, 'tag');
        return $result;
    }
}
