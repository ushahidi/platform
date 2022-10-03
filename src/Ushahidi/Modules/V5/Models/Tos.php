<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Core\Entity\Permission;
use Illuminate\Support\Facades\Input;
use Ushahidi\Modules\V5\Models\Scopes\CategoryAllowed;

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
    
   
    /**
     * Return all validation rules
     * @param null $data
     * @return array
     */
    public function getRules($data = null)
    {
        return [
            'tos_version_date'        => [
                'required',
                'date'
            ]
        ];
    }

    public function validationMessages()
    {
        return [
            'tos_version_date.required'      => trans(
                'validation.not_empty',
                ['field' => trans('fields.tos_version_date')]
            ),
            'tos_version_date.date'      => trans(
                'validation.date',
                ['field' => trans('fields.tos_version_date')]
            ),
        ];
    }
}//end class
