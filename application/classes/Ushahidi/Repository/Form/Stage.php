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

class Ushahidi_Repository_Form_Stage extends Ushahidi_Repository implements
	FormStageRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_stages';
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

	// FormStageRepository
	public function getByForm($form_id)
	{
		$query = $this->selectQuery(compact($form_id));
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
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
			])
			->select('form_stages.*');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}
}
