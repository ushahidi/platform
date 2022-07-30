<?php

/**
 * Ushahidi CSV Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Repository;

use Ushahidi\Core\Tools\SearchData;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Entity\CSV;
use Ushahidi\Contracts\Repository\Entity\CSVRepository as CSVRepositoryContract;
use Ushahidi\Core\Concerns\Event;

class CSVRepository extends OhanzeeRepository implements
    CSVRepositoryContract
{

    // Use the JSON transcoder to encode properties
    use Concerns\JsonTranscode;

    // Use Event trait to trigger events
    use Event;

    // Concerns\JsonTranscode
    protected function getJsonProperties()
    {
        return ['columns', 'maps_to', 'fixed'];
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'csv';
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $state = [
            'created'  => time(),
        ];

        return parent::create($entity->setState($state));
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        $state = [
            'updated'  => time(),
        ];

        return parent::update($entity->setState($state));
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        return new CSV($data);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['columns', 'maps_to', 'fixed', 'filename'];
    }

    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        foreach ([
                     'filename',
                 ] as $key) {
            if ($search->$key) {
                $query->where($key, '=', $search->$key);
            }
        }
    }
}
