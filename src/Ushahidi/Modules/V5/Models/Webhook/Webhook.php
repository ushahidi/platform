<?php

namespace Ushahidi\Modules\V5\Models\Webhook;

use Ushahidi\Modules\V5\Models\BaseModel;

class Webhook extends BaseModel
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
    protected $table = 'webhooks';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'url',
        'shared_secret',
        'event_type',
        'entity_type',
        'webhook_uuid',
        'from_id',
        'source_field_key',
        'destination_field_key'
    ];


    public function getCreatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
    public function getUpdatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
}//end class
