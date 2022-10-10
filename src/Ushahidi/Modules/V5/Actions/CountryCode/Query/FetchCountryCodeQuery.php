<?php

namespace Ushahidi\Modules\V5\Actions\CountryCode\Query;

use App\Bus\Query\Query;

final class FetchCountryCodeQuery implements Query
{
    const DEFAULT_LIMIT = 20;

    private $limit;
    private $page;
    private $sortBy;
    private $direction;

    public function __construct(?int $limit, ?int $page, ?string $sortBy = null, ?string $direction = null)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->direction = $direction;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }
}
