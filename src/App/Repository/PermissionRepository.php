<?php

/**
 * Ushahidi Permission Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Entity\PermissionRepository as PermissionRepositoryContract;

class PermissionRepository extends OhanzeeRepository implements
    PermissionRepositoryContract
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'permissions';
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        return new Permission($data);
    }

    public function getSearchFields()
    {
        return ['q', /* LIKE name */];
    }

    // SearchRepository
    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;

        if ($search->q) {
            $query->where('name', 'LIKE', "%" .$search->q ."%");
        }

        return $query;
    }

    // UshahidiRepository
    public function exists($permission)
    {
        return (bool) $this->selectCount(['name' => $permission]);
    }
}
