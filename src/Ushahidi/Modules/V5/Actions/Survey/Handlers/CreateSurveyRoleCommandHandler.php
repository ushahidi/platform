<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Handlers;

use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Survey\Commands\CreateSurveyRoleCommand;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Repository\Survey\SurveyRoleRepository;
use Ushahidi\Core\Entity\FormRole as SurveyRoleEntity;

class CreateSurveyRoleCommandHandler extends V5CommandHandler
{
    private $survey_role_repository;

    public function __construct(SurveyRoleRepository $survey_role_repository)
    {
        $this->survey_role_repository = $survey_role_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateSurveyRoleCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateSurveyRoleCommand $command
     * @return int
     */
    public function __invoke($command) //: int

    {
        $this->isSupported($command);
        foreach ($command->getRoleIds() as $role_id) {
            $this->survey_role_repository->create(new SurveyRoleEntity([
                'form_id' => $command->getSurveyId(),
                'role_id' => $role_id
            ]));
        }
    }
}
