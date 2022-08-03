<?php

namespace Ushahidi\App\V5\Models;

class User extends BaseModel
{
    public $timestamps = false;

    protected $table = 'users';
    /**
     * The attributes that should be mutated to dates.
     * @var array
    */
    protected $dates = ['created', 'updated'];

    /**
    * The attributes that are mass assignable.
    * @var array
    */
    protected $fillable = [
        'id',
        'email',
        'realname',
        'password',
        'role'
    ];
}
