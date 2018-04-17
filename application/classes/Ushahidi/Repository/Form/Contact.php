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

class Ushahidi_Repository_Form_Contact extends Ushahidi_Repository implements
	FormContactRepository,
	\Ushahidi\Core\Usecase\SearchRepository
{
	use \Ushahidi\Core\Traits\Event;
	protected $form_repo;
	protected $message_repo;
	protected $targeted_survey_state_repo;

	/**
	 * Construct
	 * @param Database $db
	 * @param FormRepository $form_repo
	 */
	public function __construct(
		Database $db,
		Entity\FormRepository $form_repo,
		Entity\TargetedSurveyStateRepository $targeted_survey_state_repo,
		Entity\MessageRepository $message_repo
	)
	{
		parent::__construct($db);
		$this->form_repo = $form_repo;
		$this->targeted_survey_state_repo = $targeted_survey_state_repo;
		$this->message_repo = $message_repo;

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
		return new Entity\Contact($data);
	}

	/**
	 * @param $contact
	 * @param array $data
	 * @return Entity\Contact (return the entity from the database
	 * if there's a match,or a new one if not)
	 */
	public function getEntityWithData($contact, $data = [])
	{
		$contact = $this->selectQuery(array('contact' => $contact))->execute($this->db)->current();
		if (!$contact) {
			return new Entity\Contact($data);
		}
		return new Entity\Contact($contact);
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

		if (isset($search->form_id)) {
			$query->where('targeted_survey_state.form_id', '=', $search->form_id);
		}
	}

	// FormContactRepository
	public function updateCollection(Array $entities, $form_id = null)
	{
		if (empty($entities)) {
			return;
		}
		$results = [];

		/**
		 * @TODO: not sure how to correctly solve the issue of not being able to get all the inserted ids
		 * without inserting one by one. Inserting in a foreach is gross.
		 */

		// Start transaction
		$this->db->begin();
		$invalidatedContacts =  [];
		foreach ($entities as $entity) {
			$contactOnActiveSurvey = $this->existsInActiveTargetedSurvey($entity->contact);
			if ($contactOnActiveSurvey) {
				$this->setInactiveTargetedSurvey($contactOnActiveSurvey['targeted_survey_state_id'], $form_id);
				/** force the message in the survey state to be expired
				** so we don't send outbound messages by mistake on an invalidated contact-survey
				**/
				$message = $this->message_repo->get($contactOnActiveSurvey['message_id']);
				if ($message->id) {
					$message->setState(['status' => Entity\Message::EXPIRED]);
					$this->message_repo->update($message);
				}
				$invalidatedContacts[] = [
					'contact' => $contactOnActiveSurvey['contact'],
					'contact_id' => $contactOnActiveSurvey['contact_id'],
					'form_id' => $contactOnActiveSurvey['form_id']
				];
			}
			/**
			 * @fixme this is needed because we want to have a country code in the entity as a property to be used
			 * in phone number validation but we don't want to save it
			 */
			unset($entity->country_code);
			if (!$entity->id) {
				array_push($results, $this->createNewContact($entity));
			} else {
				array_push($results, $entity->id);
			}

		}

		// Start transaction
		$this->db->commit();

		$this->emit($this->event, $results, $form_id, 'created_contact');

		return $invalidatedContacts;
	}
	private function createNewContact($entity) {
		$query = DB::insert($this->getTable())
			->columns(array_keys($entity->asArray()));
		$query->values($entity->asArray());
		$result = $query->execute($this->db);
		if (!isset($result[0])) {
			throw new HTTP_Exception_500(
				sprintf(
					'Could not create contacts. Result:  %s',
					var_export($entity, true)
				)
			);
		}
		return $result[0];
	}
	/**
	 * @param int $form_id
	 * @return Entity|Entity\Contact
	 * Returns all
	 */
	public function getByForm($form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id))
			->select('contacts.*');
		$query = $this->targetedSurveyStateJoin($query);
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	/**
	 * @param $form_id
	 * @return int
	 */
	public function deleteAllForForm($form_id)
	{
		$entities = $this->getByForm($form_id);
		return $this->executeDelete(array('id' => array_column($entities, 'id')));

	}

	public function formExistsInPostStateRepo($form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id))
			->resetSelect()
			->select([DB::expr('COUNT(*)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query);
		$res = $query
			->execute($this->db)
			->get('total');
		return (bool)$res;
	}

	/**
	 * @param int $contact_id
	 * @param int $form_id
	 * @return bool
	 */
	public function existsInFormContact($contact_id, $form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id, 'contacts.id' => $contact_id))
			->resetSelect()
			->select([DB::expr('COUNT(*)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query);
		return (bool)$query
			->execute($this->db)
			->get('total');
	}

	public function getResponses($form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id))
			->resetSelect()
			->select([DB::expr('COUNT(distinct contact_id)')]);
		$query = $this->targetedSurveyStateJoin($query);
		return (bool)$query
			->execute($this->db);
	}

	/**
	 * @param int $contact_id
	 * @param int $form_id
	 * @return bool
	 */
	public function getReceipients($form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id))
			->resetSelect()
			->select([DB::expr('COUNT(distinct contact_id)')]);
		$query = $this->targetedSurveyStateJoin($query);
		return (bool)$query
			->execute($this->db);
	}

	/**
	 * @param int $contact_id
	 * @param int $form_id
	 * @return bool
	 */
	public function existsInFormContactByContactNumber($contact, $form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id, 'contacts.contact' => $contact))
			->resetSelect()
			->select([DB::expr('COUNT(*)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query);
		return (bool)$query
			->execute($this->db)
			->get('total');
	}

	/**
	 * @param int $contact|contact number
	 * @return bool
	 *
	 */
	public function existsInActiveTargetedSurvey($contact)
	{
		$where = array(
			'contacts.contact' => $contact,
			'targeted_survey_state.survey_status' => array(Entity\TargetedSurveyState::PENDING_RESPONSE, Entity\TargetedSurveyState::RECEIVED_RESPONSE)
		);
		$query = $this->selectQuery($where)
			->resetSelect()
			->select(
				['targeted_survey_state.id', 'targeted_survey_state_id'],
				['contacts.contact', 'contact'],
				['targeted_survey_state.contact_id', 'contact_id'],
				['targeted_survey_state.form_id', 'form_id'],
				['targeted_survey_state.message_id', 'message_id']
			)
			->limit(1);
		$query = $this->targetedSurveyStateJoin($query);
		$result = $query
			->execute($this->db);
		if ($result) {
			return $result->current();
		}
	}
	/**
	 * @param int $contact_id
	 * @param int $form_id
	 * @return bool
	 */
	public function setInactiveTargetedSurvey($tss_id, $form_id)
	{
		$repo = $this->targeted_survey_state_repo->get($tss_id);
		$entity = $repo->setState(array('survey_status' => str_replace('###', $form_id,Entity\TargetedSurveyState::INVALID_CONTACT_MOVED)));
		$this->targeted_survey_state_repo->update($entity);
	}

	// SearchRepository
	public function getSearchResults()
	{
		$query = $this->getSearchQuery();
		$query = $this->targetedSurveyStateJoin($query);
		$results = $query->distinct(TRUE)->execute($this->db);
		return $this->getCollection($results->as_array());
	}


	private function targetedSurveyStateJoin($query)
	{
		return $query->join('targeted_survey_state', 'INNER')
			->on('contacts.id', '=', 'targeted_survey_state.contact_id')
			->join('posts', 'INNER')
			->on('posts.id', '=', 'targeted_survey_state.post_id');

	}

	public function getSearchTotal()
	{
		// Assume we can simply count the results to get a total
		$query = $this->getSearchQuery(true)
			->resetSelect()
			->select([DB::expr('COUNT(*)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query);
		// Fetch the result and...
		$result = $query->execute($this->db);
		// ... return the total.
		return (int)$result->get('total', 0);
	}
}