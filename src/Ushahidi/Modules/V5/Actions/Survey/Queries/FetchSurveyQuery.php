<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\SurveySearchFields;

class FetchSurveyQuery implements Query
{
    const DEFAULT_LIMIT = 0;
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "id";

    private $limit;
    private $page;
    private $sortBy;
    private $order;
    private $search_fields;
    private $format;
    private $only_fields;
    private $hydrate;




    public function __construct(
        int $limit,
        int $page,
        string $sortBy,
        string $order,
        SurveySearchFields $search_fields,
        ?string $format,
        ?string $only_fields,
        ?string $hydrate
    ) {
        $this->limit = $limit;
        $this->page = $page;
        $this->sortBy = $sortBy;
        $this->order = $order;
        $this->search_fields = $search_fields;
        $this->format = $format;
        $this->only_fields = $only_fields;
        $this->hydrate = $hydrate;
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

    public function getSearchFields()
    {
        return $this->search_fields;
    }

    public function getFormat()
    {
        return $this->format;
    }
    public function getOnlyFields()
    {
        return $this->only_fields;
    }

    public function getHydrate()
    {
        return $this->hydrate;
    }
}
