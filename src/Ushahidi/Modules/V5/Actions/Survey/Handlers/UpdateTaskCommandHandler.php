<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Models\Stage as Task;
use Ushahidi\Modules\V5\Models\Attribute as Field;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Survey\Commands\UpdateTaskCommand;
use Ushahidi\Modules\V5\Repository\Survey\TaskRepository;

use Ramsey\Uuid\Uuid;

class UpdateTaskCommandHandler extends V5CommandHandler
{

    private $task_repository;

    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === UpdateTaskCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param UpdateTaskCommand $command
     * @return int
     */
    public function __invoke($command) //: int
    {
        $this->isSupported($command);
        $this->task_repository->update($command->getEntity()->id, $command->getEntity());
        $task = $this->task_repository->findById($command->getEntity()->id);
        $this->updateTranslations(
            $task,
            $task->toArray(),
            ($command->getTranslations()),
            $command->getEntity()->id,
            'task'
        );
        $this->updateFields($command->getFields(), $task);
    }

    private function updateFields(array $input_fields, Task $task)
    {

        $added_fields = [];
        foreach ($input_fields as $field) {
            if (isset($field['id'])) {
                $field_model = $task->fields->find($field['id']);
                if (!$field_model) {
                    continue;
                }
                if ($field['type'] === 'tags') {
                    $field['options'] = $this->normalizeCategoryOptions($field['options']);
                }
                $field_model->update($field);
                $field_model = Field::find($field['id']);
            } else {
                $uuid = Uuid::uuid4();
                if ($field['type'] === 'tags') {
                    $field['options'] = $this->normalizeCategoryOptions($field['options']);
                }
                $field_model = $task->fields()->create(
                    array_merge(
                        $field,
                        [
                            'updated' => time(),
                            'key' => $uuid->toString(),
                        ]
                    )
                );
                $added_fields[] = $field_model->id;
            } //end if

            $this->updateTranslations(
                $field_model,
                $field_model->toArray(),
                ($field['translations'] ?? []),
                $field_model->id,
                'field'
            );


            $input_fields_collection = new Collection($input_fields);

            $fields_to_delete = array_diff(
                Field::select('id')->where('form_stage_id', '=', $task->id)->pluck('id')->toArray(),
                array_merge($added_fields, $input_fields_collection->groupBy('id')->keys()->toArray())
            );
            foreach ($fields_to_delete as $field_to_delete) {
                Field::where('id', $field_to_delete)->delete();
            }
        }
    } //end foreach
    private function normalizeCategoryOptions(array $options)
    {
        if (!$this->isArrayOfNumbers($options)) {
            return array_flatten(array_pluck($options, 'id'));
        }
        return $options;
    }

    private function isArrayOfNumbers(array $arr)
    {
        return $arr === array_filter($arr, 'is_numeric');
    }
}
