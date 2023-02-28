<?php

namespace Ushahidi\Modules\V5\Repository;

use Ushahidi\Contracts\Entity;
use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\UserRepository as EntityUserRepository;

class UserRepository implements EntityUserRepository
{
    public function getByEmail($email)
    {
    }

    public function isValidResetToken($token)
    {
    }

    public function getTotalCount(array $array)
    {
    }

    public function get($id)
    {
    }

    public function getEntity(?array $data = null)
    {
    }

    public function exists($id)
    {
    }

    public function create(Entity $entity)
    {
    }

    public function createMany(Collection $collection): array
    {
    }

    public function delete(Entity $entity)
    {
    }
}
