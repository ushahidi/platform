<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\SurveyStatesSearchFields;

class FetchSurveyStatsQuery implements Query
{
    private $survey_id;
    private $search_fields;

    public function __construct(int $survey_id, SurveyStatesSearchFields $search_fields)
    {
        $this->survey_id = $survey_id;
        $this->search_fields = $search_fields;
    }

    public function getSurveyId(): int
    {
        return $this->survey_id;
    }
    public function getSearchFields()
    {
        return $this->search_fields;
    }
}
