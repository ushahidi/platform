<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\PostValue;
use Ushahidi\Core\Entity\PostValueRepository;
use Ushahidi\Core\Usecase\Post\ValuesForPostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostValueRepository;

abstract class Ushahidi_Repository_Post_Value extends Ushahidi_Repository implements
	PostValueRepository,
	ValuesForPostRepository,
	UpdatePostValueRepository
{

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new PostValue($data);
	}

	// Ushahidi_Repository
	public function getSearchFields()
	{
		return [];
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Select 'key' too
		$query->select(
				$this->getTable().'.*',
				'form_attributes.key',
				'form_attributes.type'
			)
			->join('form_attributes')->on('form_attribute_id', '=', 'form_attributes.id');

		return $query;
	}

	// PostValueRepository
	public function get($id, $post_id = null, $form_attribute_id = null)
	{
		$where = array_filter(compact('id', 'post_id', 'form_attribute_id'));
		return $this->getEntity($this->selectOne($where));
	}

	// ValuesForPostRepository
	public function getAllForPost($post_id, Array $include_attributes = [])
	{
		$query = $this->selectQuery(compact('post_id'));

		if ($include_attributes) {
			$query->where('form_attributes.key', 'IN', $include_attributes);
		}

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// ValuesForPostRepository
	public function deleteAllForPost($post_id)
	{
		return $this->executeDelete(compact('post_id'));
	}

	// PostValueRepository
	public function getValueQuery($form_attribute_id, $match)
	{
		return $this->selectQuery(compact('form_attribute_id'))
			->where('value', 'LIKE', "%$match%");
	}

	// PostValueRepository
	public function getValueTable()
	{
		return $this->getTable();
	}

	// UpdatePostValueRepository
	public function createValue($value, $form_attribute_id, $post_id)
	{
		$input = compact('value', 'form_attribute_id', 'post_id');
		$input['created'] = time();

		return $this->executeInsert($input);
	}

	// UpdatePostValueRepository
	public function updateValue($id, $value)
	{
		$update = compact('value');
		if ($id && $update)
		{
			$this->executeUpdate(compact('id'), $update);
		}
	}

	// UpdatePostValueRepository
	public function deleteNotIn($post_id, Array $ids)
	{
		DB::delete($this->getTable())
			->where('post_id', '=', $post_id)
			->where('id', 'NOT IN', $ids)
			->execute($this->db);
	}

}
