<?php

namespace Ushahidi\Modules\V5\Actions\DataProvider\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\DataProvider\Queries\FetchDataProviderByIdQuery;
use Ushahidi\Modules\V5\Repository\DataProvider\DataProviderRepository;

class FetchDataProviderByIdQueryHandler extends AbstractQueryHandler
{


    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchDataProviderByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchDataProviderByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        $datasources = app('datasources');
        $source = $datasources->getSource($query->getId());
        return collect([
            'id' => $query->getId(),
            'name' => $source->getName(),
            'options' => $source->getOptions(),
            'services' => $source->getServices(),
            'inbound_fields' => $source->getInboundFields(),
        ]);
    }
}
