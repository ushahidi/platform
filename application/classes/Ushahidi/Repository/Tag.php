<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Tag;
use Ushahidi\Entity\TagRepository;
use Ushahidi\Entity\TagSearchData;
use Ushahidi\Usecase\Tag\CreateTagRepository;
use Ushahidi\Usecase\Tag\UpdateTagRepository;
use Ushahidi\Usecase\Tag\DeleteTagRepository;

class Ushahidi_Repository_Tag extends Ushahidi_Repository implements
	TagRepository,
	CreateTagRepository,
	UpdateTagRepository,
	DeleteTagRepository
{
	private $created_id;
	private $created_ts;

	private $deleted_tag;

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'tags';
	}

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new Tag($data);
	}

	// TagRepository
	public function get($id)
	{
		return $this->getEntity($this->selectOne(compact('id')));
	}

	// TagRepository
	public function search(TagSearchData $search, Array $params = null)
	{
		$where = Arr::extract($search->asArray(), ['tag', 'type']);
		if ($search->parent) {
			$where['parent_id'] = $search->parent;
		}

		// Start the query, removing empty values
		$query = $this->selectQuery(array_filter($where));

		if ($search->q) {
			// Tag text searching
			$query->where('tag', 'LIKE', "%{$search->q}%");
		}

		if (!empty($params['orderby'])) {
			$query->order_by($params['orderby'], Arr::get($params, 'order'));
		}

		if (!empty($params['offset'])) {
			$query->offset($params['offset']);
		}
		if (!empty($params['limit'])) {
			$query->limit($params['limit']);
		}

		$results = $query->execute($this->db);
		
		return $this->getCollection($results->as_array());
	}

	// CreateTagRepository
	public function createTag($tag, $slug, $description, $type, $color = null, $icon = null, $priority = 0, $role = null)
	{
		$input = compact('tag', 'slug', 'description', 'type');

		// Add optional fields
		$optional = array_filter(compact('color', 'icon', 'priority','role'));
		if ($optional) {
			$input += $optional;
		}

		$input['created'] = $this->created_ts = time();

		$this->created_id = $this->insert($input);
	}

	// CreateTagRepository
	public function getCreatedTagId()
	{
		return $this->created_id;
	}

	// CreateTagRepository
	public function getCreatedTagTimestamp()
	{
		return $this->created_ts;
	}

	// CreateTagRepository
	public function getCreatedTag()
	{
		return $this->get($this->created_id);
	}

	// UpdateTagRepository
	public function isSlugAvailable($slug)
	{
		return $this->selectCount(compact('slug')) === 0;
	}

	// UpdateTagRepository
	public function updateTag($id, Array $update)
	{
		if ($id && $update)
		{
			$this->update(compact('id'), $update);
		}
		return $this->get($id);
	}

	// DeleteTagRepository
	public function deleteTag($id)
	{
		$this->deleted_tag = $this->get($id);

		return $this->delete(compact('id'));
	}

	// DeleteTagRepository
	public function getDeletedTag()
	{
		return $this->deleted_tag;
	}

}
