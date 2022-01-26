<?php

namespace v5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostMedia extends PostValue
{
    public $table = 'post_media';

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [];
    }//end validationMessages()

    /**
     * Return all validation rules
     *
     * @return array
     */
    public function getRules()
    {
        $rules = [
            'value' => [
                'numeric',
                Rule::exists('media', 'id')
            ],
        ];
        return array_merge(parent::getRules(), $rules);
    }//end getRules()
}//end class
