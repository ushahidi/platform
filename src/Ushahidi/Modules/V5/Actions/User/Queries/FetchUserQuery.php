<?php

namespace Ushahidi\Modules\V5\Actions\User\Queries;

use App\Bus\Query\Query;

class FetchUserQuery implements Query
{
    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "id";
    const AVAILABLE_SEARCH_FIELDS = ['name','q']; // q like name
    
    private $limit;
    private $page;
    private $sortBy;
    private $order;
    private $search_data;

    public function __construct(int $limit, int $page, string $sortBy, string $order, array $search_data)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->order = $order;
        $this->search_data = $search_data;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function getSearchData()
    {
        return $this->search_data;
    }
}
