<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use App\Bus\Action;
use Ushahidi\Modules\V5\Models\Survey;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Survey\Commands\UpdateSurveyCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\UpdateTaskCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateTaskCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\DeleteTasksCommand;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;
use Ushahidi\Modules\V5\Repository\Survey\TaskRepository;
use Ushahidi\Core\Entity\FormStage as TaskEntity;
use App\Bus\Command\CommandBus;

class UpdateSurveyCommandHandler extends V5CommandHandler
{
    private $survey_repository;
    private $task_repository;

    private $commandBus;

    public function __construct(CommandBus $commandBus, SurveyRepository $survey_repository, TaskRepository $task_repository)
    {
        $this->survey_repository = $survey_repository;
        $this->task_repository = $task_repository;

        $this->commandBus = $commandBus;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === UpdateSurveyCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param UpdateSurveyCommand $command
     * @return int
     */
    public function __invoke($command) //: int

    {
        $this->isSupported($command);
        $this->survey_repository->update($command->getId(), $command->getEntity());
        $survey = new Survey();
        // update translation
        $this->updateTranslations(
            $survey,
            $survey->toArray(),
            $command->getTranslations(),
            $command->getId(),
            'survey'
        );

        // add tasks
        if ($command->getTasks()) {
            foreach ($command->getTasks() as $task_values) {
                if (isset($task_values['id'])) {
                    $task = $this->task_repository->findById($task_values['id']);
                    if ($task) {
                        $remain_task_ids[] = $task_values['id'];
                        $this->commandBus->handle(
                            new UpdateTaskCommand(
                                TaskEntity::buildEntity(
                                    $task_values,
                                    'update',
                                    $task->toArray()
                                ),
                                $task_values['fields'] ?? [],
                                $task_values['translations'] ?? []
                            )
                        );
                    }
                } else {
                    $new_task_ids[] = $this->commandBus->handle(
                        new CreateTaskCommand(
                            TaskEntity::buildEntity(array_merge($task_values, ["survey_id" => $command->getId()])),
                            $task_values['fields'] ?? [],
                            $task_values['translations'] ?? []
                        )
                    );
                }
            }
        }
        $removed_task_ids = array_diff($command->getCurrentTaskIds(), $remain_task_ids ?? []);
        $this->commandBus->handle(
            new DeleteTasksCommand(
                array_diff($command->getCurrentTaskIds(), $remain_task_ids ?? [])
            )
        );

        return $survey->id;
    }
}
