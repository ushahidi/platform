<?php

namespace Ushahidi\Modules\V5\Actions\SavedSearch\Handlers;

use App\Bus\Action;
use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\SavedSearch\Queries\FetchSavedSearchByIdQuery;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as SavedSearchRepository;

class FetchSavedSearchByIdQueryHandler extends AbstractQueryHandler
{

    private $saved_search_repository;

    public function __construct(SavedSearchRepository $saved_search_repository)
    {
        $this->saved_search_repository = $saved_search_repository;
    }

    protected function isSupported(Query $query)
    {
        assert(
            get_class($query) === FetchSavedSearchByIdQuery::class,
            'Provided query is not supported'
        );
    }


    /**
     * @param FetchSavedSearchByIdQuery $query
     * @return array
     */
    public function __invoke(Action $query) //: array
    {
        $this->isSupported($query);
        return $this->saved_search_repository->findById($query->getId(), 1);
    }
}
