<?php

namespace Ushahidi\Modules\V5\Actions\Post\Handlers;

use App\Bus\Action;
use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Actions\V5QueryHandler;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Models\RolePermission;

abstract class AbstractPostQueryHandler extends V5QueryHandler
{
    protected function userHasManagePostPermissions()
    {
        $user = Auth::user();
        if (!$user || !$user->id) {
            return false;
        }
        if ($user->role === "admin") {
            return true;
        }
        $permissions =
            RolePermission::select("permission")->where('role', '=', $user->role)->get()->pluck('permission');
        if (in_array("Manage Posts", $permissions->toArray())) {
            return true;
        }
        return false;
    }

    protected function updateSelectFieldsDependsOnPermissions(array $fields)
    {

        if (!$this->userHasManagePostPermissions()) {
            return array_diff($fields, ["author_email","author_realname"]);
        }
        return $fields;
    }
}
