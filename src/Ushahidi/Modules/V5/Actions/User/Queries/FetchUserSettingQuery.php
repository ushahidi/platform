<?php

namespace Ushahidi\Modules\V5\Actions\User\Queries;

use App\Bus\Query\Query;

class FetchUserSettingQuery implements Query
{
    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "id";

    private $limit;
    private $page;
    private $sortBy;
    private $order;
    private $user_id;

    public function __construct(int $user_id, int $limit, int $page, string $sortBy, string $order)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->order = $order;
        $this->user_id = $user_id;
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
    public function getUserId(): string
    {
        return $this->user_id;
    }
}
