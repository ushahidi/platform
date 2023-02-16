<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionQuery;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as CollectionRepository;

class FetchCollectionQueryHandler extends AbstractQueryHandler
{
    private $collection_repository;

    public function __construct(CollectionRepository $collection_repository)
    {
        $this->collection_repository = $collection_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchCollectionQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchCollectionQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator

    {
        $this->isSupported($query);
        $skip = $query->getLimit() * ($query->getPage() - 1);
        return $this->collection_repository->fetch(
            $query->getLimit(),
            $skip,
            $query->getSortBy(),
            $query->getOrder(),
            $query->getSearchData()
        );
    }
}
