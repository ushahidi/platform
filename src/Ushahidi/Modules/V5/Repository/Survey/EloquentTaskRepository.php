<?php

namespace Ushahidi\Modules\V5\Repository\Survey;

use Ushahidi\Modules\V5\Models\Stage as Task;
use Ushahidi\Modules\V5\Repository\Survey\TaskRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\FormStage as TaskEntity;

class EloquentTaskRepository implements TaskRepository
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
    ) {
        return $this->setSearchCondition(
            $survey_id,
            Task::orderBy($sortBy, $order)
        )->get();
    }

    /**
     * This method will fetch a single Task from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Task
     * @throws NotFoundException
     */
    public function findById(int $id): Task
    {
        $task = Task::find($id);
        if (!$task instanceof Task) {
            throw new NotFoundException('Task not found');
        }
        return $task;
    }

    private function setSearchCondition(?int $survey_id, $builder)
    {
        if ($survey_id) {
            $builder->where('form_id', '=', $survey_id);
        }
        return $builder;
    }

    /**
     * This method will create a Task
     * @param TaskEntity $task
     * @return int
     * @throws \Exception
     */
    public function create(TaskEntity $task_entity): Task
    {
        DB::beginTransaction();
        try {
            $Task = Task::create($task_entity->asArray());
            DB::commit();
            return $Task;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will update the Task
     * @param int @id
     * @param TaskEntity $task_entity
     * @throws NotFoundException
     */
    public function update(int $id, TaskEntity $task_entity): void
    {
        $task = Task::find($id);
        if (!$task instanceof Task) {
            throw new NotFoundException('Task not found');
        }

        DB::beginTransaction();
        try {
            Task::find($id)->fill($task_entity->asArray())->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * This method will create a Task
     * @param int $id
     * @return int
     * @throws NotFoundException
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    /**
     * This method will delete the Task
     * @param int $id
     */
    public function deleteTasks(array $task_ids): void
    {
        Task::whereIn('id', $task_ids)->delete();
    }
}
