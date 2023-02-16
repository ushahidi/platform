<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use App\Bus\Action;
use Ushahidi\Modules\V5\Models\Survey;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateSurveyCommand;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateTaskCommand;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;
use Ushahidi\Core\Entity\FormStage as TaskEntity;
use App\Bus\Command\CommandBus;

class CreateSurveyCommandHandler extends V5CommandHandler
{
    private $survey_repository;
    private $commandBus;

    public function __construct(CommandBus $commandBus, SurveyRepository $survey_repository)
    {
        $this->survey_repository = $survey_repository;
        $this->commandBus = $commandBus;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateSurveyCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateSurveyCommand $command
     * @return int
     */
    public function __invoke($command) //: int

    {
        $this->isSupported($command);
        $survey = $this->survey_repository->create($command->getEntity());

        // create translation
        $this->saveTranslations(
            $survey,
            $survey->toArray(),
            $command->getTranslations(),
            $survey->id,
            'survey'
        );

        // add tasks
        if ($command->getTasks()) {
            foreach ($command->getTasks() as $task_values) {
                $task_id = $this->commandBus->handle(
                    new CreateTaskCommand(
                        TaskEntity::buildEntity(array_merge($task_values, ["survey_id" => $survey->id])),
                        $task_values['fields'] ?? [],
                        $task_values['translations'] ?? [],
                    )
                );
            }
        }

        return $survey->id;
    }
}
