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

    public function setConfigValueAttribute($value)
    {
        if (is_bool($value)) {
            $this->attributes['config_value'] = $value ? 'true' : 'false';
        } elseif (! is_array($value)) {
            $this->attributes['config_value'] = $value;
        } else {
            $this->attributes['config_value'] =  json_encode($value) ;
        }
    }

    public function getConfigValueAttribute($value)
    {
        if ($value === 'true') {
            return true;
        } elseif ($value === 'false') {
            return false;
        }

        return json_decode($value, true)?json_decode($value, true):$value;
    }

    public function getCreatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
    public function getUpdatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
}
