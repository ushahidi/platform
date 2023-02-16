<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Queries;

use App\Bus\Query\Query;

class FetchTasksBySurveyIdQuery implements Query
{
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_SORT_BY = "priority";

    private $survey_id;
    private $sortBy;
    private $order;



    public function __construct(
        int $survey_id,
        string $sortBy,
        string $order
    ) {
        $this->survey_id = $survey_id;
        $this->sortBy = $sortBy;
        $this->order = $order;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function getSurveyId(): int
    {
        return $this->survey_id;
    }
}
