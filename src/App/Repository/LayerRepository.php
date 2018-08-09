<?php

/**
 * Ushahidi Layer Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Layer;
use Ushahidi\Core\SearchData;

class LayerRepository extends OhanzeeRepository
{
    // Use the JSON transcoder to encode properties
    use JsonTranscodeRepository;

    // OhanzeeRepository
    protected function getTable()
    {
        return 'layers';
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        return new Layer($data);
    }

    // JsonTranscodeRepository
    protected function getJsonProperties()
    {
        return ['options'];
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['active', 'type'];
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;

        if ($search->active !== null) {
            $query->where('active', '=', $search->active);
        }

        if ($search->type) {
            $query->where('type', '=', $search->type);
        }
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $record = array_filter($entity->asArray());
        $record['created'] = time();

        return $this->executeInsert($this->removeNullValues($record));
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        $update = $entity->getChanged();
        $update['updated'] = time();

        return $this->executeUpdate(['id' => $entity->id], $update);
    }
}
