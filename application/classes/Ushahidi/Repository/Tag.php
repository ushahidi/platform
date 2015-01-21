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
use Ushahidi\Core\Tool\JsonTranscode;

class Ushahidi_Repository_Tag extends Ushahidi_Repository implements
	UpdateTagRepository,
	DeleteTagRepository,
	UpdatePostTagRepository
{
	private $created_id;
	private $created_ts;

	private $deleted_tag;

	protected $json_transcoder;
	protected $json_properties = ['role'];

	public function setTranscoder(JsonTranscode $transcoder)
	{
		$this->json_transcoder = $transcoder;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'tags';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new Tag($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['tag', 'type', 'parent_id', 'q', /* LIKE tag */];
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
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$record = array_filter($this->json_transcoder->encode(
			$entity->asArray(), $this->json_properties
		)->asArray());
		$record['created'] = time();
		return $this->executeInsert($this->removeNullValues($record));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$record = $this->json_transcoder->encode(
			$entity->getChanged(), $this->json_properties
		)->asArray();
		return $this->executeUpdate(['id' => $entity->getId()], $record);
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
