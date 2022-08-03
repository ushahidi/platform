<?php

namespace Ushahidi\App\V5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PostInt extends PostValue
{
    public $table = 'post_int';
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
            'value' => ['integer'],
        ];
        return array_merge(parent::getRules(), $rules);
    }//end getRules()
}//end class
