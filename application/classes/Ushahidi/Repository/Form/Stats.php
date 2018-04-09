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
		$where = array(
			'posts.form_id' => $form_id,
			'messages.direction' => 'incoming',
			'targeted_survey_state.survey_status' => array(
				Entity\TargetedSurveyState::RECEIVED_RESPONSE,
				Entity\TargetedSurveyState::PENDING_RESPONSE,
				Entity\TargetedSurveyState::SURVEY_FINISHED,
			)
		);
		$query = $this->selectQuery($where)
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

	public function countTotalPending($form_id, $total_sent) {
		$form_id = intval($form_id);
		//total_contacts = select count(contact_id) from targeted_survey_state where form_id=8 and survey_status IN ('RECEIVED RESPONSE','PENDING RESPONSE')
		//total_attributes = select count(form_attributes.id) from form_attributes INNER JOIN form_stages ON form_attributes.form_stage_id = form_stages.id WHERE form_stages.form_id=8
		$total_contacts = DB::query(Database::SELECT,
			"select count(contact_id) as total from targeted_survey_state where form_id=$form_id
			 and survey_status NOT IN ('SURVEY FINISHED')")
		->execute($this->db)->get('total');
		$total_attributes = DB::query(Database::SELECT,
			"select count(form_attributes.id) as total from form_attributes 
			INNER JOIN form_stages ON form_attributes.form_stage_id = form_stages.id WHERE form_stages.form_id=$form_id")
			->execute($this->db)->get('total');
		$internal_query = "SELECT count(form_attributes.form_stage_id) as counted from targeted_survey_state " .
			"INNER JOIN form_stages ON targeted_survey_state.form_id=form_stages.form_id " .
			"INNER JOIN form_attributes ON form_stages.id = form_attributes.form_stage_id " .
			"INNER JOIN (SELECT form_attributes.priority, targeted_survey_state.form_attribute_id, targeted_survey_state.contact_id " .
			"FROM form_attributes " . "INNER JOIN targeted_survey_state ON form_attributes.id =targeted_survey_state.form_attribute_id " .
			"WHERE targeted_survey_state.form_id=$form_id and targeted_survey_state.survey_status='INACTIVE'";
		$query_string = "SELECT SUM(results.counted) as total FROM ($internal_query) as internal_query "
			. "ON internal_query.contact_id = targeted_survey_state.contact_id "
			. "WHERE form_attributes.priority > internal_query.priority "
			. "AND survey_status = 'INACTIVE' "
			. "AND form_stages.form_id=$form_id "
			. "group by targeted_survey_state.contact_id, form_attributes.form_stage_id) as results;";
		$total_pending_for_inactive = DB::query(Database::SELECT,
		$query_string
		)->execute($this->db)->get('total');
		//$total_sent_to_inactive = $total_pending_for_inactive > 0? $total_attributes - $total_pending_for_inactive : 0;
		return ($total_contacts * $total_attributes) - $total_sent - $total_pending_for_inactive;
	}

	public function countOutgoingMessages($form_id)
	{
		$query = $this->selectQuery()
			->reset()
			->from('messages')
			->select(DB::expr('count(messages.status) as total, messages.status'))
			->where('post_id', 'IN', DB::expr('(select post_id FROM targeted_survey_state WHERE form_id ='.$form_id.')'))
			->where('direction', '=', 'outgoing')
			->group_by('status');
		$result = $query
			->execute($this->db);
		$ret = ['pending' => 0, 'sent' => 0];
		foreach( $result->as_array()  as $item) {
			if ($item['status'] === 'pending') {
				$ret['pending'] = $item['total'];
			} else if ($item['status'] === 'sent') {
				$ret['sent'] = $item['total'];
			}
		}
		return $ret;
	}

	public function countPendingMessages($form_id)
	{
		$where = array(
			'posts.form_id' => $form_id,
			'messages.direction' => 'outgoing',
			'messages.status' => 'pending',
			'targeted_survey_state.survey_status' => array(
				Entity\TargetedSurveyState::RECEIVED_RESPONSE,
				Entity\TargetedSurveyState::PENDING_RESPONSE,
				Entity\TargetedSurveyState::SURVEY_FINISHED,
			)
		);
		$query = $this->selectQuery($where)
			->resetSelect()
			->select([DB::expr('COUNT(distinct message_id)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query)->join('messages', 'INNER')->on('messages.id', '=', 'targeted_survey_state.message_id');
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
		$where = array(
			'posts.form_id' => $form_id,
			'targeted_survey_state.survey_status' => array(
				Entity\TargetedSurveyState::RECEIVED_RESPONSE,
				Entity\TargetedSurveyState::PENDING_RESPONSE,
				Entity\TargetedSurveyState::SURVEY_FINISHED,
			)
		);
		$query = $this->selectQuery($where)
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