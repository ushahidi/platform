<?php

/**
 * Ushahidi ApiKey Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\ApiKey;
use Ushahidi\Core\Entity\ApiKeyRepository as ApiKeyRepositoryContract;
use Ushahidi\Core\Traits\AdminAccess;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class ApiKeyRepository extends OhanzeeRepository implements ApiKeyRepositoryContract
{
    use AdminAccess;

    protected function getTable()
    {
        return 'apikeys';
    }

    public function getEntity(array $data = null)
    {
        return new ApiKey($data);
    }

    // OhanzeeRepository
    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;

        return $query;
    }

    // CreateRepository
    public function create(Entity $entity)
    {

        $record = $entity->asArray();

        $uuid = Uuid::uuid4();
        $record['api_key'] = $uuid->toString();

        $state = [
            'created' => time(),
        ];

        return $this->executeInsert($this->removeNullValues($record));
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        $record = $entity->asArray();
        $record['updated'] = time();

        $uuid = Uuid::uuid4();
        $record['api_key'] = $uuid->toString();

        return $this->executeUpdate(['id' => $entity->id], $record);
    }

    public function apiKeyExists($api_key)
    {
        return (bool) $this->selectCount(compact('api_key'));
    }

    public function getSearchFields()
    {
        return [
        ];
    }
}
