<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\PostValue;
use Ushahidi\Entity\PostValueRepository;
use Ushahidi\Entity\GetValuesForPostRepository;
use Ushahidi\Usecase\Post\UpdatePostValueRepository;

abstract class Ushahidi_Repository_PostValue extends Ushahidi_Repository implements
	PostValueRepository,
	GetValuesForPostRepository,
	UpdatePostValueRepository
{

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new PostValue($data);
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Select 'key' too
		$query->select(
				$this->getTable().'.*',
				'form_attributes.key',
				'form_attributes.cardinality',
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

	// GetValuesForPostRepository
	public function getAllForPost($post_id)
	{
		$query = $this->selectQuery(compact('post_id'));

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// PostValueRepository
	public function getValueQuery($form_attribute_id, $match)
	{
		return $this->selectQuery(compact('form_attribute_id'))
			->where('value', 'LIKE', "%$match%");
	}

	// UpdatePostValueRepository
	public function createValue($value, $form_attribute_id, $post_id)
	{
		$input = compact('value', 'form_attribute_id', 'post_id');
		$input['created'] = time();

		return $this->insert($input);
	}

	// UpdatePostValueRepository
	public function updateValue($id, $value)
	{
		$update = compact('value');
		if ($id && $update)
		{
			$this->update(compact('id'), $update);
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
