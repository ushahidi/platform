<?php

namespace Ushahidi\Modules\V5\Models\HXL;

use Ushahidi\Modules\V5\Models\BaseModel;

class HXLAttribute extends BaseModel
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
    protected $table = 'hxl_attributes';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attribute',
        'description'
    ];
}//end class
