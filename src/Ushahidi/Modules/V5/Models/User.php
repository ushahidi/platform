<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Support\Collection;

class User extends BaseModel
{
    public $timestamps = false;

    protected $table = 'users';
    /**
     * The attributes that should be mutated to dates.
     * @var array
    */
    protected $dates = [];

    /**
    * The attributes that are mass assignable.
    * @var array
    */
    protected $fillable = [
        'id',
        'email',
        'realname',
        'password',
        'role',
        'language',
        'created',
        'updated'
    ];

    public function getCreatedAttribute($value)
    {
        return $value?date('Y-m-d H:i:s', $value):null;
    }
    public function getUpdatedAttribute($value)
    {
        return $value?date('Y-m-d H:i:s', $value):null;
    }

    public function getPermission(): Collection
    {
        return RolePermission::where('role', $this->role)->get();
    }
}
