<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateTaskCommand;
use Ushahidi\Modules\V5\Repository\Survey\TaskRepository;

use Ramsey\Uuid\Uuid;

class CreateTaskCommandHandler extends V5CommandHandler
{

    private $task_repository;

    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateTaskCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateTaskCommand $command
     * @return int
     */
    public function __invoke($command) //: int
    {
        $this->isSupported($command);
        $task = $this->task_repository->create($command->getEntity());
        // TO DO: create task translations
        $this->saveTranslations(
            $task,
            $task->toArray(),
            ($command->getTranslations()),
            $task->id,
            'task'
        );

        // create fields
        foreach ($command->getFields() as $field_values) {
            $uuid = Uuid::uuid4();
            $field_values['key'] = $uuid->toString();

            if ($field_values['type'] === 'tags') {
                $field_values['options'] = $this->normalizeCategoryOptions($field_values['options']);
            }
            $field = $task->fields()->create(
                array_merge(
                    $field_values,
                    ['updated' => time(), 'created' => time()]
                )
            );

            // TO DO: create field translations
            $this->saveTranslations(
                $field,
                $field->toArray(),
                ($field_values['translations'] ?? []),
                $field->id,
                'field'
            );
        }


        return $task->id;
    }

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
