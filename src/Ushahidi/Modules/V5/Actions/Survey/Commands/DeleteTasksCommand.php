<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Commands;

use App\Bus\Command\Command;

class DeleteTasksCommand implements Command
{


    /**
     * @var array
     */
    private $task_ids;

    public function __construct(array $task_ids)
    {
        $this->task_ids = $task_ids;
    }


    /**
     * @return array
     */
    public function getTaskIds(): array
    {
        return $this->task_ids;
    }
}
