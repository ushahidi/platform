<?php

namespace Ushahidi\Modules\V5\Models;

class Apikey extends BaseModel
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
    protected $table = 'apikeys';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'api_key',
        'client_id',
        'client_secret',
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
}//end class
