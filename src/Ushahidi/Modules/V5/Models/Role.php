<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Role extends Model
{
    protected $table = 'roles';

    public function getPermission(): Collection
    {
        return RolePermission::where('role', $this->name)->get();
    }
}
