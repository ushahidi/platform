<?php

/**
 * Ushahidi Data Provider Repository, using DataProvider factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository;

use Ushahidi\Core\Tool\SearchData;
use Illuminate\Support\Collection;
use Ushahidi\Contracts\Repository\ReadRepository;
use Ushahidi\Contracts\Repository\SearchRepository;
use Ushahidi\Core\Entity\DataProvider as DataProviderEntity;
use Ushahidi\Contracts\Repository\Entity\DataProviderRepository as DataProviderRepositoryContract;
use Ushahidi\Contracts\Search;

class DataProviderRepository implements
    ReadRepository,
    SearchRepository,
    DataProviderRepositoryContract
{

    public function __construct()
    {
        $this->datasources = app('datasources');
    }

    // use CollectionLoader;

    /**
     * Converts a laravel collection of data sources into an array of entities
     * indexed by the entity id.
     *
     * @param  Collection $sources
     * @return Array
     */
    protected function getCollection(Collection $sources)
    {
        return $sources->mapWithKeys(function ($source) {
            $entity = $this->getEntity([
                'id' => $source->getId(),
                'name' => $source->getName(),
                'options' => $source->getOptions(),
                'services' => $source->getServices(),
                'inbound_fields' => $source->getInboundFields(),
            ]);
            return [$source->getId() => $entity];
        })->all();
    }

    // ReadRepository
    public function getEntity(array $data = null)
    {
        return new DataProviderEntity($data);
    }

    // ReadRepository
    // DataProviderRepository
    public function get($provider)
    {
        try {
            $source = $this->datasources->getSource($provider);

            return $this->getEntity([
                'id' => $provider,
                'name' => $source->getName(),
                'options' => $source->getOptions(),
                'services' => $source->getServices(),
                'inbound_fields' => $source->getInboundFields(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->getEntity([]);
        }
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['type'];
    }

    // SearchRepository
    public function setSearchParams(Search $search)
    {
        $this->search_params = $search;
    }

    // SearchRepository
    public function getSearchResults()
    {
        $sources = collect($this->datasources->getSources())
            // Grab the actual source instances
            ->map(function ($name) {
                return $this->datasources->getSource($name);
            })
            // Only include user configurable
            ->filter(function ($source) {
                return $source->isUserConfigurable();
            });

        // Filter by type
        if ($this->search_params->type) {
            $sources = $sources->filter(function ($source) {
                return in_array($this->search_params->type, $source->getServices());
            });
        }

        $this->search_total = $sources->count();

        return $this->getCollection($sources);
    }

    // SearchRepository
    public function getSearchTotal()
    {
        return $this->search_total;
    }

    public function exists($id)
    {
    }
}
