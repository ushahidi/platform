<?php

namespace Ushahidi\Modules\V5\Models;

class UserSetting extends BaseModel
{
    public $timestamps = false;

    protected $table = 'user_settings';
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
        'config_key',
        'config_value',
        'user_id'
    ];
}
