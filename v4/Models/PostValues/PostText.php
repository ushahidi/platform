<?php

namespace v4\Models\PostValues;

class PostText extends PostValue
{
    public $table = 'post_text';

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function validationMessages()
    {
        return [
        ];
    }//end validationMessages()

    /**
     * Return all validation rules
     *
     * @return array
     */
    protected function getRules()
    {
        $rules = [
            'value' => ['string'],
        ];
        return [parent::getRules(), $rules];
    }//end getRules()
}//end class
