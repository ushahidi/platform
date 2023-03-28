<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\FormStage as TaskEntity;

class CreateTaskCommand implements Command
{
    /**
     * @var TaskEntity
     */
    private $entity;

    /**
     * @var array fields
     */
    private $fields;

    /**
     * @var array translations
     */
    private $translations;



    public function __construct(TaskEntity $task_entity, array $fields = [], array $translations = [])
    {
        $this->entity = $task_entity;
        $this->fields = $fields;
        $this->translations = $translations;
    }

    /**
     * @return TaskEntity
     */
    public function getEntity(): TaskEntity
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }
}
