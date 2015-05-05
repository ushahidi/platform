<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Attribute Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Tool\JsonTranscode;

class Ushahidi_Repository_Form_Attribute extends Ushahidi_Repository implements
	FormAttributeRepository
{
	protected $json_transcoder;
	protected $json_properties = ['options'];

	public function setTranscoder(JsonTranscode $transcoder)
	{
		$this->json_transcoder = $transcoder;
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$record = $this->json_transcoder->encode(
			$entity->asArray(),
			$this->json_properties
		);
		unset($record['form_id']);
		return $this->executeInsert($this->removeNullValues($record));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$record = $this->json_transcoder->encode(
			$entity->getChanged(),
			$this->json_properties
		);
		return $this->executeUpdate(['id' => $entity->getId()], $record);
	}

	// SearchRepository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'key', 'label', 'input', 'type'
		] as $key) {
			if (isset($search->$key)) {
				$query->where($key, '=', $search->$key);
			}
		}

		if ($search->form_id) {
			$query
				->join('form_stages', 'INNER')->on('form_stages.id', '=', 'form_attributes.form_stage_id')
				->where('form_stages.form_id', '=', $search->form_id);
		}
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_attributes';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new FormAttribute($data);
	}

	// Ushahidi_Repository
	public function getSearchFields()
	{
		return ['form_id', 'type', 'label', 'key', 'input'];
	}

	// FormAttributeRepository
	public function getByKey($key, $form_id = null)
	{
		$where = array_filter(compact('key', 'form_id'));

		$result = $this->selectQuery($where)
			->select('form_attributes.*')
			->join('form_stages', 'INNER')
				->on('form_stages.id', '=', 'form_attributes.form_stage_id')
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
	public function getByForm($form_id)
	{
		$query = $this->selectQuery([
				'form_stages.form_id' => $form_id,
			])
			->select('form_attributes.*')
			->join('form_stages', 'INNER')
				->on('form_stages.id', '=', 'form_attributes.form_stage_id');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// FormAttributeRepository
	public function getRequired($stage_id)
	{
		$query = $this->selectQuery([
				'form_attributes.form_stage_id'  => $stage_id,
				'form_attributes.required' => true
			])
			->select('form_attributes.*');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}
}
