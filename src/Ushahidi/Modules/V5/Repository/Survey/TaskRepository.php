<?php

namespace Ushahidi\Modules\V5\Repository\Survey;

use Ushahidi\Modules\V5\Models\Stage as Task;
use Ushahidi\Core\Entity\FormStage as TaskEntity;
use Ushahidi\Modules\V5\DTO\TaskSearchFields;

interface TaskRepository
{
    /**
     * This method will fetch all the Task for the logged task from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param string $sortBy
     * @param string $order
     * @param ?int $survey_id
     * @return Task[]
     */
    public function fetch(
        string $sortBy,
        string $order,
        ?int $survey_id = null
    );

    /**
     * This method will fetch a single Task from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Task
     * @throws NotFoundException
     */
    public function findById(int $id): Task;

    /**
     * This method will create a Task
     * @param TaskEntity $task_entity
     * @return Task
     */
    public function create(TaskEntity $task_entity): Task;

    /**
     * This method will update the Task
     * @param int $id
     * @param TaskEntity $task_entity
     */
    public function update(int $id, TaskEntity $task_entity): void;

    /**
     * This method will delete the Task
     * @param int $id
     */
    public function delete(int $id): void;

    /**
     * This method will delete the Task
     * @param int $id
     */
    public function deleteTasks(array $task_ids): void;
}
