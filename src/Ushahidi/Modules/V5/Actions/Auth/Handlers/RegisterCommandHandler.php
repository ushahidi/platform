<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;
use Ushahidi\Modules\V5\Actions\Auth\Commands\RegisterCommand;
use Ushahidi\Modules\V5\Repository\User\UserRepository;

class RegisterCommandHandler extends V5CommandHandler
{
    private $user_repository;
    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof RegisterCommand) {
            throw new \Exception('Provided command is not of type ' . RegisterCommand::class);
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var RegisterCommand $action
         */
        $this->isSupported($action);
        return $this->user_repository->create($action->getUserEntity());
    }
}
