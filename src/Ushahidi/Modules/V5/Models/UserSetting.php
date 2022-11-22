<?php

namespace Ushahidi\Modules\V5\Models;

class UserSetting extends BaseModel
{
    public $timestamps = false;

    protected $table = 'user_settings';
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'id',
        'config_key',
        'config_value',
        'user_id',
        'created',
        'updated'
    ];

    public function getCreatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
    public function getUpdatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
}
