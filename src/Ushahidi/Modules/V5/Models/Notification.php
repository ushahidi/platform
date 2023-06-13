<?php

namespace Ushahidi\Modules\V5\Models;

class Notification extends BaseModel
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
    protected $table = 'notifications';


    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'set_id',
        'created'
    ];
}
