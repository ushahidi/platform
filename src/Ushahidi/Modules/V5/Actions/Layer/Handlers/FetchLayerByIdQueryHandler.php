<?php

namespace Ushahidi\Modules\V5\Actions\Layer\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Layer\Queries\FetchLayerByIdQuery;
use Ushahidi\Modules\V5\Repository\Layer\LayerRepository;

class FetchLayerByIdQueryHandler extends AbstractQueryHandler
{

    private $layer_repository;

    public function __construct(LayerRepository $layer_repository)
    {
        $this->layer_repository = $layer_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchLayerByIdQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchLayerByIdQuery $query
     * @return array
     */
    public function __invoke($query) //: array
    {
        $this->isSupported($query);
        return $this->layer_repository->findById($query->getId());
    }
}
