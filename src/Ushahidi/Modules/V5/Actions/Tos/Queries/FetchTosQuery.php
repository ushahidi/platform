<?php

namespace Ushahidi\Modules\V5\Actions\Tos\Queries;

use App\Bus\Query\Query;

class FetchTosQuery implements Query
{
    const DEFAULT_LIMIT = 20;
    const DEFAULT_ORDER = "DESC";
    const DEFAULT_SORT_BY = "id";

    private $limit;
    private $page;
    private $sortBy;
    private $order;

    public function __construct(int $limit, int $page, string $sortBy, string $order)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->order = $order;
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
}
