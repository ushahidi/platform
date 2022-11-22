<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\User\Commands\UpdateUserSettingCommand;
use Ushahidi\Modules\V5\Repository\User\UserSettingRepository;

class UpdateUserSettingCommandHandler extends AbstractCommandHandler
{

    private $user_setting_repository;

    public function __construct(UserSettingRepository $user_setting_repository)
    {
        $this->user_setting_repository = $user_setting_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === UpdateUserSettingCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param UpdateUserSettingCommand $command
     * @return int
     */
    public function __invoke(Action $command) //: int
    {
        $this->isSupported($command);
        $this->user_setting_repository->update($command->getId(), $command->getEntity());
    }
}
