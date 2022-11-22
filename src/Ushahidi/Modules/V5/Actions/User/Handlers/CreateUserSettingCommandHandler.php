<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\User\Commands\CreateUserSettingCommand as CreateUserSettingCommand;
use Ushahidi\Modules\V5\Repository\User\UserSettingRepository;

class CreateUserSettingCommandHandler extends AbstractCommandHandler
{

    private $user_setting_repository;

    public function __construct(UserSettingRepository $user_setting_repository)
    {
        $this->user_setting_repository = $user_setting_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateUserSettingCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateUserSettingCommand $command
     * @return int
     */
    public function __invoke(Action $command) //: int
    {
        $this->isSupported($command);
        $command->setId(
            $this->user_setting_repository->create($command->getEntity())
        );
    }
}
