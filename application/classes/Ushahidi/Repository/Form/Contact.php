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
		return new Entity\Contact($data);
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
		foreach($entities as $entity) {
			//@fixme how to avoid this ugly line?
			unset($entity->country_code);
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
			array_push($results, $result[0]);
		}

		// Start transaction
		$this->db->commit();



		$this->emit($this->event,  $results , $form_id, 'created_contact');

		return $entities;
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

	public function formExistsInPostStateRepo($form_id) {
		$query = $this->selectQuery(array('posts.form_id' => $form_id))
			->resetSelect()
			->select([DB::expr('COUNT(*)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query);
		$res = $query
			->execute($this->db)
			->get('total');
		return (bool) $res;
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
		return (bool) $query
			->execute($this->db)
			->get('total');
	}

	public function getResponses($form_id)
	{
		$query = $this->selectQuery(array('posts.form_id' => $form_id))
			->resetSelect()
			->select([DB::expr('COUNT(distinct contact_id)')]);
		$query = $this->targetedSurveyStateJoin($query);
		return (bool) $query
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
		return (bool) $query
			->execute($this->db);
	}
//SELECT COUNT(distinct contact_id) AS `total` FROM `targeted_survey_state` INNER JOIN `posts` ON (`posts`.`id` = `targeted_survey_state`.`post_id`) WHERE `posts`.`form_id` = 1;
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
		return (bool) $query
			->execute($this->db)
			->get('total');
	}

	// SearchRepository
	public function getSearchResults()
	{
		$query = $this->getSearchQuery();
		$query = $this->targetedSurveyStateJoin($query);
		$results = $query->distinct(TRUE)->execute($this->db);
		return $this->getCollection($results->as_array());
	}


	private function targetedSurveyStateJoin($query) {
		return $query->join('targeted_survey_state', 'INNER')
			->on('contacts.id', '=', 'targeted_survey_state.contact_id')
			->join('posts', 'INNER')
			->on('posts.id', '=', 'targeted_survey_state.post_id');

	}

	public function getSearchTotal() {

		// Assume we can simply count the results to get a total
		$query = $this->getSearchQuery(true)
			->resetSelect()
			->select([DB::expr('COUNT(*)'), 'total']);
		$query = $this->targetedSurveyStateJoin($query);
		// Fetch the result and...
		$result = $query->execute($this->db);
		// ... return the total.
		return (int) $result->get('total', 0);
	}
}