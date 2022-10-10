<?php

namespace Ushahidi\Modules\V5\Repository\Role;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Modules\V5\Models\Role;

interface RoleRepository
{
    /**
     * @param string $role
     * @return Model|Role
     */
    public function findByRole(string $role): Role;
}
