<?php

/**
 * Ushahidi Data Provider Repository, using DataProvider factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\SearchData;
use Ushahidi\App\DataSource\DataSource;
use Ushahidi\Core\Entity\DataProvider as DataProviderEntity;
use Ushahidi\Core\Entity\DataProviderRepository as DataProviderRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\Core\Traits\CollectionLoader;
use Ushahidi\Core\Exception\NotFoundException;

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
     * Converts an array of results into an array of entities,
     * indexed by the entity id.
     * @param  Array $results
     * @return Array
     */
    protected function getCollection(array $results)
    {
        $collection = [];
        foreach ($results as $id => $row) {
            $entity = $this->getEntity([
                'id' => $id,
                'name' => $row->getName(),
                'options' => $row->getOptions(),
                'services' => $row->getServices(),
                'inbound_fields' => $row->getInboundFields(),
            ]);
            $collection[$entity->getId()] = $entity;
        }
        return $collection;
    }

    // ReadRepository
    public function getEntity(array $data = null)
    {
        return new DataProviderEntity($data);
    }

    /**
     * Get all enabled providers, with their configuration data.
     * @return Array
     */
    protected function getAllProviders()
    {
        // Returns all providers, even if they are disabled.
        return array_filter($this->datasources->getSource(), function ($source) {
            return $source->isUserConfigurable();
        });
    }

    // ReadRepository
    // DataProviderRepository
    public function get($provider)
    {
        $source = $this->datasources->getSource($provider);

        if (!$source) {
            return $this->getEntity([]);
        }

        return $this->getEntity([
            'id' => $provider,
            'name' => $source->getName(),
            'options' => $source->getOptions(),
            'services' => $source->getServices(),
            'inbound_fields' => $source->getInboundFields(),
        ]);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['type'];
    }

    // SearchRepository
    public function setSearchParams(SearchData $search)
    {
        $this->search_params = $search;
    }

    // SearchRepository
    public function getSearchResults()
    {
        $providers = $this->getAllProviders();

        foreach ($providers as $name => $source) {
            if ($this->search_params->type) {
                if (!in_array($this->search_params->type, $source->getServices())) {
                    // Provider does not offer this type of service, skip it.
                    unset($providers[$name]);
                }
            }
        }

        $this->search_total = count($providers);

        return $this->getCollection($providers);
    }

    // SearchRepository
    public function getSearchTotal()
    {
        return $this->search_total;
    }
}
