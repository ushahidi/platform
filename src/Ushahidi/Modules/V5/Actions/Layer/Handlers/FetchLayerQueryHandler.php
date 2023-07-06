<?php

namespace Ushahidi\Modules\V5\Actions\Layer\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Layer\Queries\FetchLayerQuery;
use Ushahidi\Modules\V5\Repository\Layer\LayerRepository;

class FetchLayerQueryHandler extends AbstractQueryHandler
{
    private $layer_repository;

    public function __construct(LayerRepository $layer_repository)
    {
        $this->layer_repository = $layer_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchLayerQuery::class,
            'Provided query is not supported'
        );
    }
    
    /**
     * @param FetchLayerQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        return $this->layer_repository->fetch(
            $query->getPaging(),
            $query->getSearchFields()
        );
    }
}
