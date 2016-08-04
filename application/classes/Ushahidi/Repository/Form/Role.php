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
use Ushahidi\Core\Entity;
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
		return ['form_id', 'roles'];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->form_id) {
			$query->where('form_id', '=', $search->form_id);
		}

		if ($search->roles) {
			$query->where('role_id', 'in', $search->roles);
		}
	}
	
	// UpdateRepository
	public function update(Entity $entity)
	{
		$form_id = $entity->form_id;
		$form_role = new FormRole();
		$entity_array = [];
		
		$this->deleteAllForForm($form_id);
		
		foreach($entity->roles as $role_id)
		{
			$state = [
				'form_id'  => $form_id,
				'role_id'  => $role_id,
			];
	
			$entity_id = parent::create($form_role->setState($state));
			
			$new_role = [
				'id'       => $entity_id,
				'form_id'  => $form_id,
				'role_id'  => $role_id,				
			];

			array_push($entity_array, $new_role);
		}

		return $entity_array;
	}	

	// FormRollRepository
	public function getByForm($form_id)
	{
		$query = $this->selectQuery(compact($form_id));
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// ValuesForFormRoleRepository
	public function deleteAllForForm($form_id)
	{
		return $this->executeDelete(compact('form_id'));
	}

	// FormRollRepository
	public function existsInFormRole($role_id, $form_id)
	{
		return (bool) $this->selectCount(compact('role_id', 'form_id'));
	}

}
