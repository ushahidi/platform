<?php

namespace Ushahidi\Modules\V5\Models;

class ExportJob extends BaseModel
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
    protected $table = 'export_job';


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'entity_type',
        'fields',
        'filters',
        'status',
        'url',
        'url_expiration',
        'status_details',
        'header_row',
        'hxl_meta_data_id',
        'include_hxl',
        'send_to_browser',
        'send_to_hdx',
        'hxl_heading_row',
        'total_rows',
        'total_batches',
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
    
    public function getIncludeHxlAttribute($value)
    {
        return (bool)$value;
    }
    public function getSendToBrowserAttribute($value)
    {
        return (bool)$value;
    }
    public function getSendToHdxAttribute($value)
    {
        return (bool)$value;
    }

    public function setFiltersAttribute($value)
    {
        $this->attributes['filters'] = $value ? json_encode($value) : null;
    }
    public function getFiltersAttribute($value)
    {
        return   json_decode($value, true)?? $value;
    }
    
    public function setFieldsAttribute($value)
    {
        $this->attributes['fields'] = $value ? json_encode($value) : null;
    }
    public function getFieldsAttribute($value)
    {
        return   json_decode($value, true)?? $value;
    }

    public function setHeaderRowAttribute($value)
    {
        $this->attributes['header_row'] = $value ? json_encode($value) : null;
    }
    public function getHeaderRowAttribute($value)
    {
        return   json_decode($value, true)?? $value;
    }

    public function setHxlHedingRowAttribute($value)
    {
        $this->attributes['hxl_heading_row'] = $value ? json_encode($value) : null;
    }
    public function getHedingRowAttribute($value)
    {
        return   json_decode($value, true)?? $value;
    }
}//end class
