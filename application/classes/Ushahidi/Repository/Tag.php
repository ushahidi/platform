<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Tag;
use Ushahidi\Core\Usecase\Tag\UpdateTagRepository;
use Ushahidi\Core\Usecase\Tag\DeleteTagRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Repository_Tag extends Ushahidi_Repository implements
	UpdateTagRepository,
	DeleteTagRepository,
	UpdatePostTagRepository
{
	// Use the JSON transcoder to encode properties
	use Ushahidi_JsonTranscodeRepository;
	// Use trait to for updating forms_tags-table
	use Ushahidi_FormsTagsTrait;
	private $created_id;
	private $created_ts;

	private $deleted_tag;

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'tags';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		if (!empty($data['id'])) 
		{
			$data['forms'] = $this->getFormsForTag($data['id']);
	
        if(empty($data['parent_id'])) {
    
            $data['children'] = 
            DB::select('id')
            ->from('tags')
            ->where('parent_id','=',$data['id'])
            ->execute($this->db)
            ->as_array();
        }
    }
		return new Tag($data);
	}

	// Ushahidi_JsonTranscodeRepository
	protected function getJsonProperties()
	{
		return ['role'];
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['tag', 'type', 'parent_id', 'q', 'level', 'formId' /* LIKE tag */];
	}

	// Ushahidi_Repository
    protected function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        foreach (['tag', 'type', 'parent_id'] as $key) 
        {
            if ($search->$key) {
                 $query->where($key, '=', $search->$key);
            }
        }
        if ($search->q) {
            // Tag text searching
            $query->where('tag', 'LIKE', "%{$search->q}%");
        }
        if($search->level) {
            //searching for top-level-tags 
            if($search->level === 'parent') {
                $query->where('parent_id', '=', null);
            }
        }
        if($search->formId){
        	$query->join('forms_tags')
        		->on('tags.id', '=', 'forms_tags.tag_id')
        		->where('form_id','=', $search->formId);
        }
    } 
	// SearchRepository
	public function getSearchResults()
	{
		$query = $this->getSearchQuery();
		$results = $query->distinct(TRUE)->execute($this->db);
		return $this->getCollection($results->as_array());
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$record = $entity->asArray();
		$record['created'] = time();

		unset($record['forms']);

		$id = $this->executeInsert($this->removeNullValues($record));

		if($entity->forms) {
			//updating forms_tags-table
			$this->updateTagForms($id, $entity->forms);
		}

		return $id;
	}
	
	public function update(Entity $entity)
	{
		$tag = $entity->getChanged();
		unset($tag['forms']);

		$count = $this->executeUpdate(['id' => $entity->id], $tag);
		// updating forms_tags-table
		if($entity->hasChanged('forms'))
		{
			$this->updateTagForms($entity->id, $entity->forms);
		}

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
			->select([DB::expr('COUNT(*)'), 'total'])
			->where('id', '=', $tag_or_id)
			->or_where('tag', '=', $tag_or_id)
			->execute($this->db);

		return $query->get('total') > 0;
	}

	// UpdateTagRepository
	public function isSlugAvailable($slug)
	{
		return $this->selectCount(compact('slug')) === 0;
	}
    public function delete(Entity $entity)
    {
        $this->updateFormAttributes($entity->id);
        return $this->executeDelete([
            'id' => $entity->id
        ]);
    }

	// DeleteTagRepository
	public function deleteTag($id)
	{
        $this->updateFormAttributes($entity->id);
		return $this->delete(compact('id'));
	}
}
