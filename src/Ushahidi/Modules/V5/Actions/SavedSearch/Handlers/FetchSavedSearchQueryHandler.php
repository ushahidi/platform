<?php

namespace Ushahidi\Modules\V5\Actions\SavedSearch\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\SavedSearch\Queries\FetchSavedSearchQuery;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as SavedSearchRepository;

class FetchSavedSearchQueryHandler extends AbstractQueryHandler
{
    private $saved_search_repository;

    public function __construct(SavedSearchRepository $saved_search_repository)
    {
        $this->saved_search_repository = $saved_search_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchSavedSearchQuery::class,
            'Provided query is not supported'
        );
    }

    /**
     * @param FetchSavedSearchQuery $query
     * @return LengthAwarePaginator
     */
    public function __invoke($query) //: LengthAwarePaginator
    {
        $this->isSupported($query);
        $skip = $query->getLimit() * ($query->getPage() - 1);
        return $this->saved_search_repository->fetch(
            $query->getLimit(),
            $skip,
            $query->getSortBy(),
            $query->getOrder(),
            $query->getSearchData()
        );
    }
}
