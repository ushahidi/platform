<?php

namespace Ushahidi\Modules\V5\Models\Webhook;

use Ushahidi\Modules\V5\Models\BaseModel;

class WebhookJob extends BaseModel
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
    protected $table = 'webhook_job';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'event_type',
        'created'
    ];
}//end class
