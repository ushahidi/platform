<?php

namespace Ushahidi\Modules\V5\Actions\SavedSearch\Handlers;

use App\Bus\Query\AbstractQueryHandler;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\SavedSearch\Queries\FetchSavedSearchQuery;
use Ushahidi\Modules\V5\Repository\Set\SetRepository as SavedSearchRepository;
use Ushahidi\Core\Tool\SearchData;
use Illuminate\Support\Facades\Auth;

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

        $search_fields = $query->getSearchData();

        $search = new SearchData();
        $user = Auth::guard()->user();

        $search->setFilter('keyword', $search_fields->q());
        $search->setFilter('role', $search_fields->role());
        $search->setFilter('is_admin', $search_fields->role() == "admin");
        // $search->setFilter('is_guest', !Auth::user() || !Auth::user()->id);
        // $search->setFilter('is_me_only', $search_fields->public());
        $search->setFilter('user_id', $user->id ?? null);

        $search->setFilter('is_saved_search', true);

        // Paging Values
        $limit = $query->getLimit() ?? config('paging.default_laravel_pageing_limit');
        $search->setFilter('limit', $limit);
        $search->setFilter('skip', $limit * ($query->getPage() - 1));

        // Sorting Values
        $search->setFilter('sort', $query->getSortBy());
        $search->setFilter('order', $query->getOrder());

        $this->saved_search_repository->setSearchParams($search);

        return $this->saved_search_repository->fetch();
    }
}
