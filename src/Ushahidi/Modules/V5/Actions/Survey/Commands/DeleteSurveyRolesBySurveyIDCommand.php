<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Commands;

use App\Bus\Command\Command;

class DeleteSurveyRolesBySurveyIDCommand implements Command
{

    /**
     * @var array
     */
    private $survey_id;

    public function __construct(int $survey_id)
    {
        $this->survey_id = $survey_id;
    }

    /**
     * @return int
     */
    public function getSurveyId(): int
    {
        return $this->survey_id;
    }
}
