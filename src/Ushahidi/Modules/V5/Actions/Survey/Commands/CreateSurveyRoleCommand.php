<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Commands;

use App\Bus\Command\Command;

class CreateSurveyRoleCommand implements Command
{
    /**
     * @var array
     */
    private $role_ids;

    private $survey_id;
    public function __construct($survey_id, array $role_ids)
    {
        $this->role_ids = $role_ids;
        $this->survey_id = $survey_id;
    }
    /**
     * @return array
     */
    public function getRoleIds(): array
    {
        return $this->role_ids;
    }

    public function getSurveyId(): int
    {
        return $this->survey_id;
    }
}
