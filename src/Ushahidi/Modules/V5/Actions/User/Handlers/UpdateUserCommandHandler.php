<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\User\Commands\UpdateUserCommand;
use Ushahidi\Modules\V5\Repository\User\UserRepository;

class UpdateUserCommandHandler extends AbstractCommandHandler
{

    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === UpdateUserCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param UpdateUserCommand $command
     * @return int
     */
    public function __invoke(Action $command) //: int
    {
        $this->isSupported($command);
        $this->user_repository->update($command->getId(), $command->getEntity());
    }
}
