<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class RolePermissions extends BaseModel
{
    /**
     * Specify the table
     *
     * @var string
     */
    protected $table = 'roles_permissions';

    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;



    /**
     * @var array
     */
    protected $fillable = [
        'role',
        'permission'
    ];

    public function permission()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Permissions', 'name', 'permission');
    }

    public function role()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Role', 'name', 'role');
    }
}//end class
