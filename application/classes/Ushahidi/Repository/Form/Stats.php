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
		return ['form_id', 'contacts', 'created_after', 'created_before'];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->form_id) {
			$query->where('form_id', '=', $search->form_id);
		}
	}

    /**
     * @param $query
     * @param $column
     * @param null $before
     * @param null $after
     * @return mixed
     */
	private function betweenDates($query, $column, $before_dt = null, $after_dt = null) {
        if ($before_dt && $after_dt) {
            $query->where($column, 'BETWEEN', [strtotime($after_dt), strtotime($before_dt)]);
        } else if ($before_dt) {
            $query->where($column, '<=', strtotime($before_dt));
        } else if ($after_dt) {
            $query->where($column, '>=', strtotime($after_dt));
        }
        return $query;
    }
	public function getResponses($form_id, $created_after, $created_before)
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
        $query = $this->selectQuery($where);

        $query = $this->betweenDates($query,'posts.created', $created_before, $created_after);

        $query
            ->resetSelect()
            ->select([DB::expr('COUNT(messages.id)'), 'total']);
        $query
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
	 * @param $form_id
	 * @param $total_sent
	 * @return float|int
	 * Returns the number of TOTAL pending questions (for invalidated+valid contacts off a
	 * targeted_survey)
	 */
	public function countTotalPending($form_id, $total_sent) {
		$form_id = intval($form_id);
		$total_contacts = $this->getTotalContacts($form_id);
		$total_attributes =$this->getTotalAttributes($form_id);
		$total_pending_for_inactive = DB::query(Database::SELECT, $this->getPendingCountQuery())
			->bind(':form_id', $form_id)
			->execute($this->db)->get('total');
		return ($total_contacts * $total_attributes) - $total_sent - $total_pending_for_inactive;
	}

	/**
	 * @return string | sql query to get pending questions for all invalidated contacts
	 * on a targeted_survey_state group
	 * Does not receive form_id because it is bound in a later step
	 */
	private function getPendingCountQuery() {
		/**
		 * Selects attribute priority & id by contact,for contacts marked in targeted_survey_state as Inactive
		 * (noted by the ACTIVE CONTACT IN SURVEY  # format of survey_status)
		 */
		$attributeListQuery = "SELECT form_attributes.priority, targeted_survey_state.form_attribute_id, targeted_survey_state.contact_id " .
  			"FROM form_attributes " .
			"INNER JOIN targeted_survey_state ON form_attributes.id =targeted_survey_state.form_attribute_id " .
  			"WHERE targeted_survey_state.form_id=:form_id and targeted_survey_state.survey_status LIKE 'ACTIVE CONTACT IN SURVEY%'";
		/**
		 * counts attributes that have a priority higher than the one of the attributess referenced
		 * in targeted_survey_state for each invalidated contact
		 */
		$attributeCountQuery = "SELECT count(form_attributes.form_stage_id) as counted from targeted_survey_state " .
			"INNER JOIN form_stages ON targeted_survey_state.form_id=form_stages.form_id " .
 			"INNER JOIN form_attributes ON form_stages.id = form_attributes.form_stage_id " .
 			"INNER JOIN ($attributeListQuery) as internal_query " .
 			"ON internal_query.contact_id = targeted_survey_state.contact_id " .
 			"WHERE form_attributes.priority > internal_query.priority " .
 			"AND survey_status LIKE 'ACTIVE CONTACT IN SURVEY%' AND form_stages.form_id=:form_id " .
 			"GROUP BY targeted_survey_state.contact_id, form_attributes.form_stage_id";
		/**
		 * sums the result of the previous joined queries to gget how many attributes where not yet sent
		 * for invalidated contacts
		 */
		$sql = "SELECT SUM(results.counted) as total FROM ($attributeCountQuery) as results";
		return $sql;
	}

	/**
	 * @param $form_id
	 * @return mixed
	 */
	private function getTotalAttributes($form_id) {
		return DB::query(Database::SELECT,
			"select count(form_attributes.id) as total from form_attributes 
			INNER JOIN form_stages ON form_attributes.form_stage_id = form_stages.id WHERE form_stages.form_id=:form")
			->bind(':form', $form_id)
			->execute($this->db)->get('total');
	}

	/**
	 * @param $form_id
	 * @return mixed
	 */
	private function getTotalContacts($form_id) {
		return DB::query(Database::SELECT,
			"select count(contact_id) as total from targeted_survey_state where form_id=:form
			 and survey_status NOT IN ('SURVEY FINISHED')")
			->bind(':form', $form_id)
			->execute($this->db)->get('total');
	}

	/**
	 * @param $form_id
	 * @return array
	 */
	public function countOutgoingMessages($form_id, $created_after, $created_before)
	{
		$query = $this->selectQuery()
			->reset()
			->from('messages')
			->select(DB::expr('count(messages.status) as total, messages.status'))
			->where('post_id', 'IN', DB::expr('(select post_id FROM targeted_survey_state WHERE form_id ='.$form_id.')'))
			->where('direction', '=', 'outgoing')
            ->group_by('status');
        $query = $this->betweenDates($query,'created', $created_before, $created_after);
        $result = $query
			->execute($this->db);
		$ret = ['pending' => 0, 'sent' => 0];
		foreach( $result->as_array()  as $item ) {
			if ($item['status'] === 'pending') {
				$ret['pending'] = $item['total'];
			} else if ($item['status'] === 'sent') {
				$ret['sent'] = $item['total'];
			}
		}
		return $ret;
	}

	/**
	 * @param $form_id
	 * @return mixed
	 */
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
	public function getRecipients($form_id, $created_after, $created_before)
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
            ->select([DB::expr('COUNT(contacts.id)'), 'total']);

        $query = $this->targetedSurveyStateJoin($query);
        if ($created_after || $created_before) {
            $query
                ->join('messages', 'INNER')
                ->on('messages.contact_id', '=', 'contacts.id')
                ->where('messages.direction', '=', 'outgoing');
            $query = $this->betweenDates($query,'messages.created', $created_before, $created_after);
        }
		return $query
			->execute($this->db)
			->get('total');
	}

    /**
     * @param $query
     * @param int $form_id
     * @param string $created_after
     * @param string $created_before
     * @param $result
     * Count of Unique Responders to Targeted Survey
     * @return array
     */
    public function getResponseRecipients($form_id, $created_after, $created_before)
    {
        
        $query = DB::select()
            ->from('posts')
            ->where('form_id', '=', $form_id);

        $query = $this->betweenDates($query,'created', $created_before, $created_after);

        $query = DB::select([DB::expr('COUNT(contact_id)'), 'total'])
            ->distinct(true)
            ->from([$query,'targeted_posts'])
            ->join('messages', 'INNER')
            ->on('messages.post_id', '=', 'targeted_posts.id')
            ->where('messages.direction','=', 'incoming');

        return $query
            ->execute($this->db)
            ->get('total');
    }

	/**
	 * @param $query
	 * @return mixed
	 * A reusable join because we use it everywhere.
	 */
	private function targetedSurveyStateJoin($query) {
		return $query->join('targeted_survey_state', 'INNER')
			->on('contacts.id', '=', 'targeted_survey_state.contact_id')
			->join('posts', 'INNER')
			->on('posts.id', '=', 'targeted_survey_state.post_id');
    }
    
    public function getSurveyType($form_id)
    {
        $query = DB::select('targeted_survey')
        ->from('forms')
        ->where('id', '=', $form_id);
        $results = $query->execute($this->db);
        return $results->as_array();
    }

    public function getPostCountByDataSource($form_id, $created_after, $created_before)
    {
        if ($created_after) {
            $created_after = strtotime($created_after);
        }
        if ($created_before) {
            $created_before = strtotime($created_before);
        }
        $dataSourceCounts = $this->queryByDataSource($form_id, $created_after, $created_before);
        $result = [
            'sms' => $dataSourceCounts['sms'],
            'email' => $dataSourceCounts['email'],
            'twitter' => $dataSourceCounts['twitter'],
            'web' => $this->queryForWeb($form_id, $created_after, $created_before),
        ];
        $result['all'] = $result['web'] + $result['email'] + $result['twitter'] + $result['sms'];
        return $result;
    }

    private function queryByDataSource($form_id, $created_after, $created_before)
    {
        $query = DB::select('messages.type', [DB::expr('COUNT(messages.id)'), 'total'])
            ->from('posts')
            ->join('messages', 'INNER')
            ->on('messages.post_id', '=', 'posts.id')
            ->where('posts.form_id', '=', $form_id)
            ->group_by('messages.type');

        $query = $this->betweenDates($query,'messages.created', $created_before, $created_after);

        $result = $query
            ->execute($this->db);

        $ret = ['sms' => 0, 'email' => 0, 'twitter' => 0];
        foreach ($result->as_array() as $item) {
            if ($item['type'] === 'sms') {
                $ret['sms'] = $item['total'];
            } elseif ($item['type'] === 'email') {
                $ret['email'] = $item['total'];
            } elseif ($item['type'] === 'twitter') {
                $ret['twitter'] = $item['total'];
            }
        }

        return $ret;
    }

    private function queryForWeb($form_id, $created_after, $created_before)
    {
        $query = DB::select([DB::expr('COUNT(posts.id)'), 'total'])
            ->from('posts')
            ->join('messages', 'LEFT')
            ->on('messages.post_id', '=', 'posts.id')
            ->where('messages.post_id', 'is', null)
            ->where('posts.form_id', '=', $form_id);
        $query = $this->betweenDates($query,'posts.created', $created_before, $created_after);
        $query->and_where('posts.type', '=', 'report');
        return $query
            ->execute($this->db)
            ->get('total');
    }
}
