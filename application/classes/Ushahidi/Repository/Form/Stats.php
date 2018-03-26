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
	FormContactRepository,
	\Ushahidi\Core\Usecase\SearchRepository
{
	use \Ushahidi\Core\Traits\Event;
	protected $form_repo;
	protected $contact_post_state_repo;

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

		if ($search->form_id) {
			$query->where('form_id', '=', $search->form_id);
		}
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
		$query = $this->contactPostStateJoin($query);
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	/**
	 * @param  int $contact_id
	 * @param  int $form_id
	 * @return [Ushahidi\Core\Entity\FormContact, ...]
	 */
	public function existsInFormContact($contact_id, $form_id)
	{
		// TODO: Implement existsInFormContact() method.
	}

	/**
	 * @param  [Ushahidi\Core\Entity\FormContact, ...]  $entities
	 * @return [Ushahidi\Core\Entity\FormContact, ...]
	 */
	public function updateCollection(array $entities)
	{
		// TODO: Implement updateCollection() method.
	}
}