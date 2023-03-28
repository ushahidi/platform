<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\Form as SurveyEntity;

class CreateSurveyCommand implements Command
{
    /**
     * @var SurveyEntity
     */
    private $entity;

    private $tasks;

    private $translations;

    public function __construct(SurveyEntity $survey_entity, array $tasks = [], array $translations = [])
    {
        $this->entity = $survey_entity;
        $this->tasks = $tasks;
        $this->translations = $translations;
    }

    /**
     * @return SurveyEntity
     */
    public function getEntity(): SurveyEntity
    {
        return $this->entity;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }
}
