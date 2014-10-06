<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Data;
use Ushahidi\SearchData;
use Ushahidi\Entity\Tag;
use Ushahidi\Usecase\CreateRepository;
use Ushahidi\Usecase\ReadRepository;
use Ushahidi\Usecase\DeleteRepository;
use Ushahidi\Usecase\SearchRepository;
use Ushahidi\Usecase\Tag\UpdateTagRepository;
use Ushahidi\Usecase\Tag\DeleteTagRepository;
use Ushahidi\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Repository_Tag extends Ushahidi_Repository implements
	CreateRepository,
	ReadRepository,
	DeleteRepository,
	SearchRepository,
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

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		if ($data['role'] && is_string($data['role'])) {
			$data['role'] = json_decode($data['role']);
		}
		return new Tag($data);
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->tag) {
			$query->where('tag', '=', $search->tag);
		}
		if ($search->type) {
			$query->where('type', '=', $search->type);
		}
		if ($search->parent) {
			$query->where('parent_id', '=', $search->parent);
		}

		if ($search->q) {
			// Tag text searching
			$query->where('tag', 'LIKE', "%{$search->q}%");
		}
	}

	// CreateRepository
	public function create(Data $data)
	{
		$record = array_filter($data->asArray());
		$record['created'] = time();

		if ($data->role) {
			$record['role'] = json_encode($record['role']);
		}

		return $this->executeInsert($record);
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

	// DeleteTagRepository
	public function deleteTag($id)
	{
		return $this->delete(compact('id'));
	}
}
