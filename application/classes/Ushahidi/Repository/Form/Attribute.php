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
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Traits\UserContext;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Ushahidi_Repository_Form_Attribute extends Ushahidi_Repository implements
	FormAttributeRepository
{
	use UserContext;

	protected $form_stage_repo;

	protected $form_repo;

	protected $form_id;

	// Use the JSON transcoder to encode properties
	use Ushahidi_JsonTranscodeRepository;
	use Ushahidi_FormsTagsTrait;

	/**
	 * Construct
	 * @param Database                              $db
	 * @param FormStageRepository                   $form_stage_repo
	 * @param FormRepository                   $form_repo
	 */
	public function __construct(
			Database $db,
			FormStageRepository $form_stage_repo,
			FormRepository $form_repo
		)
	{
		parent::__construct($db);

		$this->form_stage_repo = $form_stage_repo;
		$this->form_repo = $form_repo;

	}

	// Ushahidi_JsonTranscodeRepository
	protected function getJsonProperties()
	{
		return ['options', 'config'];
	}

	protected function getFormId($form_stage_id)
	{
		$form_id = $this->form_stage_repo->getFormByStageId($form_stage_id);
		if ($form_id) {
			return $form_id;
		}
		return null;
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [], $form_id = null, $form_stage_id = null)
	{
		$query = parent::selectQuery($where);

		if (!$form_id && $form_stage_id) {
			$form_id = $this->getFormId();
		}

		return $query;
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$record = $entity->asArray();
		unset($record['form_id']);
		try {
			$uuid = Uuid::uuid4();
			$record['key'] = $uuid->toString();
		} catch (UnsatisfiedDependencyException $e) {
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}
		return $this->executeInsertAttribute($this->removeNullValues($record));
	}

	// Override SearchRepository
	public function setSearchParams(SearchData $search)
	{
		$form_id = null;
		if ($search->form_id) {
			$form_id = $search->form_id;
		}

		$this->search_query = $this->selectQuery([], $form_id);

		$sorting = $search->getSorting();

		if (!empty($sorting['orderby'])) {
			$this->search_query->order_by(
				$this->getTable() . '.' . $sorting['orderby'],
				Arr::get($sorting, 'order')
			);
		}

		if (!empty($sorting['offset'])) {
			$this->search_query->offset($sorting['offset']);
		}

		if (!empty($sorting['limit'])) {
			$this->search_query->limit($sorting['limit']);
		}

		// apply the unique conditions of the search
		$this->setSearchConditions($search);
	}

	// SearchRepository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'key', 'label', 'input', 'type'
		] as $key) {
			if (isset($search->$key)) {
				$query->where('form_attributes.'.$key, '=', $search->$key);
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
	public function getByKey($key, $form_id = null, $include_no_form = false)
	{

		$query = $this->selectQuery([], $form_id)
			->select('form_attributes.*')
			->join('form_stages', 'LEFT')
				->on('form_stages.id', '=', 'form_attributes.form_stage_id')
			->where('key', '=', $key)
			->limit(1);

		if ($form_id) {
			$query
				->and_where_open()
				->where('form_id', '=', $form_id);

			if ($include_no_form) {
				$query->or_where('form_id', 'IS', null);
			}

			$query->and_where_close();
		}

		$result = $query->execute($this->db);
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
			], $form_id)
			->select('form_attributes.*')
			->join('form_stages', 'INNER')
				->on('form_stages.id', '=', 'form_attributes.form_stage_id');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// FormAttributeRepository
	public function getRequired($stage_id)
	{
	 $form_id = $this->getFormId($stage_id);

		$query = $this->selectQuery([
				'form_attributes.form_stage_id'  => $stage_id,
				'form_attributes.required' => true
			], $form_id)
			->select('form_attributes.*');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// FormAttributeRepository
	public function isKeyAvailable($key)
	{
		return $this->selectCount(compact('key')) === 0;
	}
}
