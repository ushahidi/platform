<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Commands;

use App\Bus\Command\Command;

class DeleteSurveyCommand implements Command
{


    /**
     * @var int
     */
    private $id;
    private $task_ids;
    private $field_ids;

    public function __construct(int $id, array $task_ids = [], array $field_ids = [])
    {
        $this->id = $id;
        $this->task_ids = $task_ids;
        $this->field_ids = $field_ids;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getTaskIds(): array
    {
        return $this->task_ids;
    }

    /**
     * @return array
     */
    public function getFieldIds(): array
    {
        return $this->field_ids;
    }
}
