<?php

namespace Ushahidi\Modules\V5\Actions\DataProvider\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\DataProvider\Queries\FetchDataProviderQuery;

class FetchDataProviderQueryHandler extends AbstractQueryHandler
{

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchDataProviderQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchDataProviderQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query)
    {
        $this->isSupported($query);
        $sources = $this->getSource($query);
        $data =  $sources->mapWithKeys(function ($source) {
            $entity = [
                'id' => $source->getId(),
                'name' => $source->getName(),
                'options' => $source->getOptions(),
                'services' => $source->getServices(),
                'inbound_fields' => $source->getInboundFields(),
            ];
            return [$source->getId() => $entity];
        })->all();

        return collect(array_values($data));
    }

    private function getSource($query)
    {
        $datasources = app('datasources');
        $sources = collect($datasources->getSources())
        // Grab the actual source instances
        ->map(function ($name) use ($datasources) {
            return $datasources->getSource($name);
        })
        // Only include user configurable
        ->filter(function ($source) {
            return $source->isUserConfigurable();
        });

    //Filter by type
        if ($query->getSearchFields()->type()) {
            $sources = $sources->filter(function ($source) use ($query) {
                return in_array($query->getSearchFields()->type(), $source->getServices());
            });
        }
        return $sources;
    }
}
