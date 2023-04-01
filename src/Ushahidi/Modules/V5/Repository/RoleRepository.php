<?php

namespace Ushahidi\Modules\V5\Repository;

use Ushahidi\Core\Entity\Role;
use Ushahidi\Core\EloquentRepository;
use Ushahidi\Contracts\Repository\Entity\RoleRepository as RoleRepositoryInterface;

/**
 * @uses Illuminate\Database\Query\Builder
 */
class RoleRepository extends EloquentRepository implements RoleRepositoryInterface
{
    protected static $root = Role::class;

    public function doRolesExist(?array $roles = null)
    {
        return $roles ? $this->whereIn('role', $roles)->exists() : true;
    }

    public function getByName($name)
    {
        return $this->where('name', $name)->first();
    }
}
