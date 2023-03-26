<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Queries;

use App\Bus\Command\Command;

class ReplaceRolesCanCreateSurveyPostsCommand implements Command
{

    private $survey_id;
    private $roles;

    public function __construct(int $survey_id, array $roles)
    {
        $this->survey_id = $survey_id;
        $this->roles = $roles;
    }
    public function getRoles(): array
    {
        return $this->roles;
    }
    public function getSurveyId(): int
    {
        return $this->survey_id;
    }
}
