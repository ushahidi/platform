<?php

namespace Ushahidi\Modules\V5\Actions\Role\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\RoleSearchFields;

class FetchRoleQuery implements Query
{
    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "id";
    
    private $limit;
    private $page;
    private $sortBy;
    private $order;
    private $role_search_fields;

    public function __construct(int $limit, int $page, string $sortBy, string $order, RoleSearchFields $role_search_fields)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->order = $order;
        $this->role_search_fields = $role_search_fields;
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

    public function getRoleSearchFields()
    {
        return $this->role_search_fields;
    }
}
