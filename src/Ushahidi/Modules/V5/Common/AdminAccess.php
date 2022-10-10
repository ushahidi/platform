<?php

namespace Ushahidi\Modules\V5\Common;

use Ushahidi\Modules\V5\Models\User as User;

trait AdminAccess
{
    /**
     * Check if the user has an Admin role
     * @param  User  $user
     * @return boolean
     */
    protected function isUserAdmin(User $user)
    {
        return ($user->id && $user->role === 'admin');
    }
}
