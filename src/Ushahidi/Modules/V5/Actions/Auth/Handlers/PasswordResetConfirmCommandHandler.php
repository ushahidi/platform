<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Repository\User\UserRepository;
use Ushahidi\Modules\V5\Actions\Auth\Commands\PasswordResetConfirmCommand;
use Ushahidi\Modules\V5\Actions\V5CommandHandler;

class PasswordResetConfirmCommandHandler extends V5CommandHandler
{
    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof PasswordResetConfirmCommand) {
            throw new \Exception('Provided $command is not instance of PasswordResetConfirmCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateContactCommand $action
         */
        $this->isSupported($action);
        if ($this->user_repository->isValidResetToken($action->getToken())) {
            $this->user_repository->setPassword($action->getToken(), $action->getNewPassword());
            $this->user_repository->deleteResetToken($action->getToken());
            return 1;
        } else {
            $errors['token'][] = "Invalid or expired reset token";
            $this->failedValidation($errors);
            return 0;
        }
    }
}
