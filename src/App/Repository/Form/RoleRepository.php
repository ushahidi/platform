<?php

/**
 * Ushahidi Form Role Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Form;

use Ohanzee\DB;
use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\FormRole;
use Ushahidi\Core\Entity\FormRoleRepository as FormRoleRepositoryContract;
use Ushahidi\App\Repository\OhanzeeRepository;
use Ushahidi\App\Repository\JsonTranscodeRepository;

class RoleRepository extends OhanzeeRepository implements
    FormRoleRepositoryContract
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'form_roles';
    }

    // CreateRepository
    // ReadRepository
    public function getEntity(array $data = null)
    {
        return new FormRole($data);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['form_id', 'roles'];
    }

    // OhanzeeRepository
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

    // FormRoleRepository
    public function updateCollection(array $entities)
    {
        if (empty($entities)) {
            return;
        }

        // Delete all existing form roles records
        // Assuming all entites have the same form id
        $this->deleteAllForForm(current($entities)->form_id);

        $query = DB::insert($this->getTable())
            ->columns(array_keys(current($entities)->asArray()));

        foreach ($entities as $entity) {
            $query->values($entity->asArray());
        }

        $query->execute($this->db);

        return $entities;
    }

    // FormRoleRepository
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

    // FormRoleRepository
    public function existsInFormRole($role_id, $form_id)
    {
        return (bool) $this->selectCount(compact('role_id', 'form_id'));
    }
}
