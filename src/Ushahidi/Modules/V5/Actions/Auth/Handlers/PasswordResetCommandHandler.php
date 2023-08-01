<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Auth\Commands\PasswordResetCommand;
use Ushahidi\Modules\V5\Repository\User\UserRepository;

class PasswordResetCommandHandler extends AbstractCommandHandler
{
    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Command $command)
    {
        if (!$command instanceof PasswordResetCommand) {
            throw new \Exception('Provided $command is not instance of PasswordResetCommand');
        }
    }

    /**
     * @param PasswordResetCommand|Action $action
     * @return int Identifier of newly created record in the database.
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);
        $user = $this->user_repository->findByEmail($action->getEmail());
        if (!$user) {
            return 0;
        }
        $code = $this->user_repository->getResetToken($user->id);
        $mailer = service('tool.mailer');
        $mailer->send(
            $user->email,
            'resetpassword',
            [
                'user_name' => $user->realname,
                'code' => $code,
                'string' => base64_encode($code),
                'duration' => 30,
            ]
        );
        return 1;
    }
}
