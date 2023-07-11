<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Config extends BaseModel
{
    const AVIALABLE_CONFIG_GROUPS = [
        'features',
        'site',
        'deployment_id',
        'test',
        'data-provider',
        'map',
        'twitter',
        'gmail'
    ];
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
    protected $table = 'config';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_name',
        'config_key',
        'config_value',
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

        return json_decode($value, true);
    }
} //end class
