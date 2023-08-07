<?php

namespace Ushahidi\Modules\V5\Models\HXL;

use Ushahidi\Modules\V5\Models\BaseModel;

class HXLAttributeTypeTag extends BaseModel
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
    protected $table = 'hxl_attribute_type_tag';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_attribute_type',
        'hxl_tag_id'
    ];
}//end class
