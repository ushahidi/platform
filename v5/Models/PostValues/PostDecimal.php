<?php

namespace v5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PostDecimal extends PostValue
{
    public $table = 'post_decimal';


    /**
     * Returns the attributes that can be translated for this model
     * @return string[]
     */
    public static function translatableAttributes():array
    {
        return [];
    }

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
