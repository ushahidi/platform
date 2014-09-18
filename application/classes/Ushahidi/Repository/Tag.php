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
use Ushahidi\Usecase\Tag\SearchTagData;
use Ushahidi\Usecase\Tag\SearchTagRepository;
use Ushahidi\Usecase\Tag\ReadTagRepository;
use Ushahidi\Usecase\Tag\CreateTagRepository;
use Ushahidi\Usecase\Tag\UpdateTagRepository;
use Ushahidi\Usecase\Tag\DeleteTagRepository;
use Ushahidi\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Repository_Tag extends Ushahidi_Repository implements
	SearchTagRepository,
	ReadTagRepository,
	CreateTagRepository,
	UpdateTagRepository,
	DeleteTagRepository,
	UpdatePostTagRepository
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

	// UpdatePostTagRepository
	public function getByTag($tag)
	{
		return $this->getEntity($this->selectOne(compact('tag')));
	}

	// TagRepository
	public function search(SearchTagData $search)
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

		if ($search->orderby) {
			$query->order_by($search->orderby, $search->order);
		}

		if ($search->offset) {
			$query->offset($search->offset);
		}
		if ($search->limit) {
			$query->limit($search->limit);
		}

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
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
		return $this->delete(compact('id'));
	}
}
