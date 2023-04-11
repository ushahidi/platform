<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionPostByIdQuery;
use Ushahidi\Modules\V5\Repository\Set\SetPostRepository as CollectionPostRepository;

class FetchCollectionPostByIdQueryHandler extends AbstractQueryHandler
{

    private $collection_post_repository;

    public function __construct(CollectionPostRepository $collection_post_repository)
    {
        $this->collection_post_repository = $collection_post_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchCollectionPostByIdQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchCollectionPostByIdQuery $action
     * @return array
     */
    public function __invoke($action) //: array
    {
        $this->isSupported($action);
        return $this->collection_post_repository->findById($action->getCollectionId(), $action->getPostId());
    }
}
