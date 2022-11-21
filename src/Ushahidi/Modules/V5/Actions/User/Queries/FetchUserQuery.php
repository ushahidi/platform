<?php

namespace Ushahidi\Modules\V5\Actions\User\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\UserSearchFields;

class FetchUserQuery implements Query
{
    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "id";
    
    private $limit;
    private $page;
    private $sortBy;
    private $order;
    private $user_search_fields;
    


    public function __construct(int $limit, int $page, string $sortBy, string $order, UserSearchFields $user_search_fields)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->order = $order;
        $this->user_search_fields = $user_search_fields;
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

    public function getUserSearchFields()
    {
        return $this->user_search_fields;
    }
}
