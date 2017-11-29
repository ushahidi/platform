<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Stage Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\FormStage;
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Traits\PostValueRestrictions;
use Ushahidi\Core\Traits\UserContext;

use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Tool\Permissions\AclTrait;

class Ushahidi_Repository_Form_Stage extends Ushahidi_Repository implements
	FormStageRepository
{
	use UserContext;

	// Provides `acl`
	use AclTrait;

	use PostValueRestrictions;

	// Checks if user is Admin
	use AdminAccess;

	protected $form_id;
	protected $form_repo;

		/**
		 * Construct
		 * @param Database                              $db
		 * @param FormRepository                       $form_repo
		 */
		public function __construct(
				Database $db,
				FormRepository $form_repo
			)
		{
			parent::__construct($db);

			$this->form_repo = $form_repo;

		}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_stages';
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [], $form_id = null, $post_status = null)
	{
		$query = parent::selectQuery($where);

		$user = $this->getUser();
		if (!$this->canUserEditForm($form_id, $user)) {
			$query->where('show_when_published', '=', "1");

			if ($post_status !== 'published') {
				$query->where('task_is_internal_only', '=', "0");
			}
		}

		return $query;
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new FormStage($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['form_id', 'label', 'postStatus'];
	}

	// Override SearchRepository
	public function setSearchParams(SearchData $search)
	{
		$form_id = null;
		if ($search->form_id) {
			$form_id = $search->form_id;
		}

		$post_status = $search->postStatus ? $search->postStatus : '';

		$this->search_query = $this->selectQuery([], $form_id, $post_status);

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

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->form_id) {
			$query->where('form_id', '=', $search->form_id);
		}

		if ($search->q) {
			// Form group text searching
			$query->where('label', 'LIKE', "%{$search->q}%");
		}
	}

	public function getFormByStageId($id)
	{
		$query = DB::select('form_id')
				->from('form_stages')
				->where('id', '=', $id);

		$results = $query->execute($this->db);

		return count($results) > 0 ? $results[0]['form_id'] : false;
	}

	// FormStageRepository
	public function getByForm($form_id)
	{
		$query = $this->selectQuery(compact($form_id), $form_id);
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	/**
		* Retrieve Hidden Stage IDs for a given form
		* if no form is found return false
		* @param  $form_id
		* @return Array
		*/
	public function getHiddenStageIds($form_id, $post_status = null)
	{
			$stages = [];

			$query = DB::select('id')
					->from('form_stages')
					->where('form_id', '=', $form_id);

			if ($post_status === 'published') {
				$query->where('show_when_published', '=', 0);
			} else {
				$query->and_where_open()
					->where('show_when_published', '=', 0)
					->or_where('task_is_internal_only', '=', 1)
					->and_where_close();
			}

			$results = $query->execute($this->db)->as_array();

			foreach($results as $stage) {
				array_push($stages, $stage['id']);
			}

			return $stages;
	}

	// FormStageRepository
	public function existsInForm($id, $form_id)
	{
		return (bool) $this->selectCount(compact('id', 'form_id'));
	}

	// FormStageRepository
	public function getRequired($form_id)
	{
		$query = $this->selectQuery([
				'form_stages.form_id'  => $form_id,
				'form_stages.required' => true
			], $form_id)
			->select('form_stages.*');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// FormStageRepository
	public function getPostStage($form_id)
	{
		return $this->getEntity($this->selectOne([
				'form_stages.form_id'  => $form_id,
				'form_stages.type' => 'post'
			]));
	}
}
