<?php

namespace Ushahidi\Modules\V5\Actions\User\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\User\Commands\CreateUserCommand;
use Ushahidi\Modules\V5\Repository\User\UserRepository;

class CreateUserCommandHandler extends AbstractCommandHandler
{

    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateUserCommand::class,
            'Provided command not supported'
        );
    }


    /**
     * run the command handler
     * @param CreateUserCommand $command
     * @return int
     */
    public function __invoke(Action $command) //: int
    {
        $this->isSupported($command);
        $command->setId(
            $this->user_repository->create($command->getEntity())
        );
    }
}
