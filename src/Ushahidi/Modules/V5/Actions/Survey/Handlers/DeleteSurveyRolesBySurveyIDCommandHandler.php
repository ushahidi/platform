<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Survey\Commands\DeleteSurveyRolesBySurveyIDCommand;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRoleRepository;

class DeleteSurveyRolesBySurveyIDCommandHandler extends V5CommandHandler
{

    private $survey_role_repository;

    public function __construct(SurveyRoleRepository $survey_role_repository)
    {
        $this->survey_role_repository = $survey_role_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === DeleteSurveyRolesBySurveyIDCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param DeleteSurveyRolesBySurveyIDCommand $command
     * @return int
     */
    public function __invoke($command) //: int
    {
        $this->isSupported($command);
        $this->survey_role_repository->deleteBySurveyId($command->getSurveyId());
    }
}
