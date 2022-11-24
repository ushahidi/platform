<?php

namespace Ushahidi\Modules\V5\Models;

class Role extends BaseModel
{
    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Specify the table to load with Survey
     *
     * @var string
     */
    protected $table = 'roles';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'display_name',
        'protected'
    ];

    public function getProtectedAttribute($value): bool
    {
        return $value;
    }

    public function permissions()
    {
        return $this->hasMany('Ushahidi\Modules\V5\Models\RolePermissions', 'role', 'name');
    }
}//end class
