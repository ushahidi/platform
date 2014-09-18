<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Attribute Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\FormAttribute;
use Ushahidi\Entity\FormAttributeRepository;

class Ushahidi_Repository_FormAttribute extends Ushahidi_Repository implements FormAttributeRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_attributes';
	}

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new FormAttribute($data);
	}

	// FormAttributeRepository
	public function get($id)
	{
		return $this->getEntity($this->selectOne(compact('id')));
	}

	// FormAttributeRepository
	public function getByKey($key, $form_id = null)
	{
		$where = array_filter(compact('key', 'form_id'));

		$result = $this->selectQuery($where)
			->select('form_attributes.*')
			->join('form_groups_form_attributes', 'INNER')
				->on('form_attributes.id', '=', 'form_attribute_id')
			->join('form_groups', 'INNER')
				->on('form_groups_form_attributes.form_group_id', '=', 'form_groups.id')
			->limit(1)
			->execute($this->db);
		return $this->getEntity($result->current());
	}

	// FormAttributeRepository
	public function getAll()
	{
		$query = $this->selectQuery();

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// FormAttributeRepository
	public function getRequired($form_id)
	{
		$query = $this->selectQuery([
				'form_id'  => $form_id,
				'required' => true
			])
			->select('form_attributes.*')
			->join('form_groups_form_attributes', 'INNER')
				->on('form_attributes.id', '=', 'form_attribute_id')
			->join('form_groups', 'INNER')
				->on('form_groups_form_attributes.form_group_id', '=', 'form_groups.id');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}
}
