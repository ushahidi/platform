<?php

namespace Ushahidi\Modules\V5\Models\HXL;

use Ushahidi\Modules\V5\Models\BaseModel;

class HXLMetaData extends BaseModel
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
    protected $table = 'hxl_meta_data';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'private',
        'dataset_title',
        'license_id',
        'user_id',
        'organisation_id',
        'source',
        'organisation_name',
        'created',
        'updated'

    ];
}//end class
