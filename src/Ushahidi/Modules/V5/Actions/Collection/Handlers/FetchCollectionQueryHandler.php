<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Core\Tool\Authorizer\SetAuthorizer;
use Ushahidi\Modules\V5\Actions\Collection\Queries\FetchCollectionQuery;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as CollectionRepository;
use Ushahidi\Core\Tool\SearchData;
use Illuminate\Support\Facades\Auth;

use function JmesPath\search;

class FetchCollectionQueryHandler extends AbstractQueryHandler
{
    private $collection_repository;

    /**
     * @var SetAuthorizer
     */
    private $setAuthorizer;

    public function __construct(
        CollectionRepository $collection_repository,
        SetAuthorizer $setAuthorizer
    ) {
        $this->collection_repository = $collection_repository;
        $this->setAuthorizer = $setAuthorizer;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchCollectionQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchCollectionQuery $action
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * phpcs:disable
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        $only_fields =  array_unique(array_merge($query->getFields(), $query->getFieldsForRelationship()));
        return $this->collection_repository->paginate(
            $query->getPaging(),
            $query->getSearchFields(),
            $only_fields,
            $query->getWithRelationship()
        );
    }
}
