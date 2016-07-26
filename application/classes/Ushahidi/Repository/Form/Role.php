<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Role Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\FormRole;
use Ushahidi\Core\Entity\FormRoleRepository;

class Ushahidi_Repository_Form_Role extends Ushahidi_Repository implements
	FormRoleRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_roles';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new FormRole($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['form_id', 'role_id'];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->form_id) {
			$query->where('form_id', '=', $search->form_id);
		}

		if ($search->role_id) {
			$query->where('role_id', '=', $search->roll_id);
		}
	}

	// FormRollRepository
	public function getByForm($form_id)
	{
		$query = $this->selectQuery(compact($form_id));
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// FormRollRepository
	public function existsInForm($id, $form_id)
	{
		return (bool) $this->selectCount(compact('id', 'form_id'));
	}

}
