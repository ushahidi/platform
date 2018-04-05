<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Contact Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\FormContactRepository;

class Ushahidi_Repository_Form_Stats extends Ushahidi_Repository implements
	Entity\FormStatsRepository,
	\Ushahidi\Core\Usecase\SearchRepository
{
	use \Ushahidi\Core\Traits\Event;
	protected $form_repo;
	protected $targeted_survey_state_repo;

	/**
	 * Construct
	 * @param Database                              $db
	 * @param FormRepository                       $form_repo
	 */
	public function __construct(
		Database $db,
		Entity\FormRepository $form_repo
	)
	{
		parent::__construct($db);

		$this->form_repo = $form_repo;

	}
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'contacts';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new Entity\FormStats($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['form_id', 'contacts'];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->form_id) {
			$query->where('form_id', '=', $search->form_id);
		}
	}
	public function getResponses($form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id, 'messages.direction' => 'incoming'))
			->resetSelect()
			->select([DB::expr('COUNT(messages.id)'), 'total'])
			->join('targeted_survey_state', 'INNER')
				->on('contacts.id', '=', 'targeted_survey_state.contact_id')
				->join('posts', 'INNER')
				->on('posts.id', '=', 'targeted_survey_state.post_id')
				->join('messages')
				->on('messages.post_id', '=', 'targeted_survey_state.post_id');
		return $query
			->execute($this->db)
			->get('total');
	}
	/**
	 * @param int $contact_id
	 * @param int $form_id
	 * @return bool
	 */
	public function getRecipients($form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => intval($form_id)))
			->resetSelect()
			->select([DB::expr('COUNT(distinct contact_id)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query);
		return $query
			->execute($this->db)
			->get('total');
	}

	private function targetedSurveyStateJoin($query) {
		return $query->join('targeted_survey_state', 'INNER')
			->on('contacts.id', '=', 'targeted_survey_state.contact_id')
			->join('posts', 'INNER')
			->on('posts.id', '=', 'targeted_survey_state.post_id');
	}
}