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
use Ushahidi\Core\Traits\PostValueRestrictions;
use Ushahidi\Core\Traits\UserContext;

class Ushahidi_Repository_Form_Stage extends Ushahidi_Repository implements
	FormStageRepository
{
	use UserContext;

	use PostValueRestrictions;

	protected $form_id;

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_stages';
	}

	protected function isRestricted ()
	{

		$user = $this->getUser();
		if ($this->form_id) {
			return $this->isFormRestricted($this->form_id, $user);
		}

		return false;
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		if ($this->isRestricted()) {
			$query->where('show_when_published', '=', '1');
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
		return ['form_id', 'label'];
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
		$this->$form_id = $form_id;
		$query = $this->selectQuery(compact($form_id));
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	/**
		* Retrieve Hidden Stage IDs for a given form
		* if no form is found return false
		* @param  $form_id
		* @return Array
		*/
	public function getHidenStageIds($form_id)
	{
			$stages = [];

			$query = DB::select('id')
					->from('form_stages')
					->where('show_when_published', '=', 0);

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
		$this->$form_id = $form_id;
		$query = $this->selectQuery([
				'form_stages.form_id'  => $form_id,
				'form_stages.required' => true
			])
			->select('form_stages.*');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}
}
