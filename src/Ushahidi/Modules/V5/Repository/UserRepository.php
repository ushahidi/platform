<?php

namespace Ushahidi\Modules\V5\Repository;

use Ushahidi\Core\Entity\User;
use Illuminate\Support\Collection;
use Ushahidi\Core\EloquentRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository as UserRepositoryInterface;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    protected static $root = User::class;

    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function isValidResetToken($token)
    {
    }

    public function getTotalCount(array $array)
    {
    }

    public function createMany(Collection $collection)
    {
    }
}
