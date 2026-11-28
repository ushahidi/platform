<?php

namespace Ushahidi\Modules\V5\Actions\Auth\Handlers;

use App\Bus\Action;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Repository\User\UserRepository;
use Ushahidi\Modules\V5\Actions\Auth\Queries\CheckOldPasswordQuery;
use Ushahidi\Modules\V5\Actions\V5QueryHandler;
use Ushahidi\Core\Tool\Hasher\Password as PasswordHash;

class CheckOldPasswordQueryHandler extends V5QueryHandler
{
    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    protected function isSupported(Query $query): void
    {
        if (!$query instanceof CheckOldPasswordQuery) {
            throw new \Exception('Provided $query is not instance of CheckOldPasswordQuery');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateContactCommand $action
         */
        $this->isSupported($action);
        $user = $this->user_repository->findByEmail($action->getEmail());
        if ($user) {
            $password_to_check = (new PasswordHash())->hash($action->getPasswordToCheck());
            if ($password_to_check === $user->password) {
                return 1;
            }
            // password not correct
            return 0;
        } else {
            // user not found
            return 0;
        }
    }
}
