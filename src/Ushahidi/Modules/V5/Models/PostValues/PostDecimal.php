<?php

namespace Ushahidi\Modules\V5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PostDecimal extends PostValue
{
    public $table = 'post_decimal';

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [
        ];
    }//end validationMessages()

    /**
     * Return all validation rules
     *
     * @return array
     */
    public function getRules()
    {
        $rules = [
            'value' => ['numeric'],
        ];
        return array_merge(parent::getRules(), $rules);
    }//end getRules()
}//end class
