<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\User\Commands\DeleteUserCommand;
use Ushahidi\Modules\V5\Repository\User\UserRepository;

class DeleteUserCommandHandler extends AbstractCommandHandler
{

    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === DeleteUserCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param DeleteUserCommand $command
     * @return int
     */
    public function __invoke(Action $command) //: int
    {
        $this->isSupported($command);
        $this->user_repository->delete($command->getId());
    }
}
