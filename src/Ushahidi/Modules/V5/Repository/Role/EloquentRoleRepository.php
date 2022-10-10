<?php

namespace Ushahidi\Modules\V5\Repository\Role;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Modules\V5\Models\Role;

class EloquentRoleRepository implements RoleRepository
{

    /**
     * @return Role|Model
     */
    public function findByRole(string $role): Role
    {
        return Role::query()->where('name', $role)->first();
    }
}
