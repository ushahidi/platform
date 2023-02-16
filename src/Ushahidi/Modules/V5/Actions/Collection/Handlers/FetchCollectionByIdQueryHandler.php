<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionByIdQuery;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as CollectionRepository;

class FetchCollectionByIdQueryHandler extends AbstractQueryHandler
{

    private $collection_repository;

    public function __construct(CollectionRepository $collection_repository)
    {
        $this->collection_repository = $collection_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchCollectionByIdQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchCollectionByIdQuery $query
     * @return array
     */
    public function __invoke(Action $query) //: array

    {
        $this->isSupported($query);
        return $this->collection_repository->findById($query->getId());
    }
}
