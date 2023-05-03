<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\Form as SurveyEntity;

class UpdateSurveyCommand implements Command
{

    private $id;

    /**
     * @var SurveyEntity
     */
    private $entity;

    private $tasks;
    private $translations;
    private $current_task_ids;
    public function __construct(
        int $id,
        SurveyEntity $survey_entity,
        array $tasks = [],
        array $translations = [],
        array $current_task_ids = []
    ) {
        $this->id = $id;
        $this->entity = $survey_entity;
        $this->tasks = $tasks;
        $this->translations = $translations;
        $this->current_task_ids = $current_task_ids;
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getCurrentTaskIds(): array
    {
        return $this->current_task_ids;
    }
}
