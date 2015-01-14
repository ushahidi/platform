<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Attribute Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
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
	public function create(Data $input)
	{
		$record = $this->json_transcoder->encode(
			$input, $this->json_properties
		)->asArray();
		unset($record['form_id']);
		return $this->executeInsert($record);
	}

	// UpdateRepository
	public function update($id, Data $input)
	{
		$record = $this->json_transcoder->encode(
			$input, $this->json_properties
		)->asArray();
		unset($record['form_id']);
		return $this->executeUpdate(compact('id'), $record);
	}

	// SearchRepository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'key', 'label', 'input', 'type'
		] as $key) {
			if (property_exists($search, $key)) {
				$query->where($key, '=', $search->$key);
			}
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
		$data = $this->json_transcoder->decode($data, $this->json_properties);
		return new FormAttribute($data);
	}

	// FormAttributeRepository
	public function getByKey($key, $form_id = null)
	{
		$where = array_filter(compact('key', 'form_id'));

		$result = $this->selectQuery($where)
			->select('form_attributes.*')
			->join('form_groups', 'INNER')
				->on('form_groups.id', '=', 'form_attributes.form_group_id')
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
			->join('form_groups', 'INNER')
				->on('form_groups.id', '=', 'form_attributes.form_group_id');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}
}
