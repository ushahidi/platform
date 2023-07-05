<?php

namespace Ushahidi\Modules\V5\Models;

class Tos extends BaseModel
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
    protected $table = 'tos';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'agreement_date',
        'tos_version_date'
    ];

    public function setTosVersionDateAttribute($value)
    {
        $value = date_create($value)->format("U");
        $this->attributes['tos_version_date'] = $value;
    }
    public function setAgreementDateAttribute($value)
    {
        $value = date("U", $value);
        $this->attributes['agreement_date'] = $value;
    }


    public function getTosVersionDateAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
    public function getAgreementDateAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}//end class
