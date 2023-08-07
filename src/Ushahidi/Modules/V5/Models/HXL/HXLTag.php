<?php

namespace Ushahidi\Modules\V5\Models\HXL;

use Ushahidi\Modules\V5\Models\BaseModel;

class HXLTag extends BaseModel
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
    protected $table = 'hxl_tags';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_name',
        'description'
    ];

    public function attributes()
    {
        return $this->belongsToMany(
            HXLAttribute::class,
            'hxl_tag_attributes',
            'hxl_tag_attributes.tag_id',
            'hxl_tag_attributes.attribute_id'
        );
    }

    public function types()
    {
        return $this->hasMany(
            HXLAttributeTypeTag::class,
            'hxl_tag_id'
        );
    }
}//end class
