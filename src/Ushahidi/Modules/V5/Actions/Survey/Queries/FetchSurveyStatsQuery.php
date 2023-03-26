<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\DTO\SurveySearchFields;

class FetchSurveyStatsQuery implements Query
{
    private $survey_id;

    public function __construct(int $survey_id)
    {
        $this->survey_id = $survey_id;
    }

    public function getSurveyId(): int
    {
        return $this->survey_id;
    }
}
