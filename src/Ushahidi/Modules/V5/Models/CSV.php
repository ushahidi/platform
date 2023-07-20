<?php

namespace Ushahidi\Modules\V5\Models;

class CSV extends BaseModel
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
    protected $table = 'csv';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'columns',
        'maps_to',
        'fixed',
        'filename',
        'size',
        'mime',
        'status',
        'errors',
        'processed',
        'collection_id',
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

     public function setColumnsAttribute($value)
     {
         $this->attributes['columns'] = $value ? json_encode($value) : null;
     }
     public function getColumnsAttribute($value)
     {
         return   json_decode($value, true)?? $value;
     }

     public function setMapsToAttribute($value)
     {
         $this->attributes['maps_to'] = $value ? json_encode($value) : null;
     }
     public function getMapsToAttribute($value)
     {
         return   json_decode($value, true)?? $value;
     }


     public function setFixedAttribute($value)
     {
         $this->attributes['fixed'] = $value ? json_encode($value) : null;
     }
     public function getFixedAttribute($value)
     {
         return   json_decode($value, true)?? $value;
     }



     public function setErrorsAttribute($value)
     {
         $this->attributes['errors'] = $value ? json_encode($value) : null;
     }
     public function getErrorsAttribute($value)
     {
         return   json_decode($value, true)?? $value;
     }
}//end class
