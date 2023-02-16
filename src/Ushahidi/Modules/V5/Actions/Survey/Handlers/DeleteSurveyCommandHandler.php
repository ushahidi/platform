<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Survey\Commands\DeleteSurveyCommand;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRepository;
use App\Bus\Command\CommandBus;

class DeleteSurveyCommandHandler extends V5CommandHandler
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
            get_class($command) === DeleteSurveyCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param DeleteSurveyCommand $command
     * @return int
     */
    public function __invoke($command) //: int
    {
        $this->isSupported($command);
        $this->deleteListTranslations($command->getFieldIds(), 'field');
        $this->deleteListTranslations($command->getTaskIds(), 'task');
        $this->deleteTranslations($command->getId(), 'survey');
        $this->survey_repository->delete($command->getId());
    }
}
