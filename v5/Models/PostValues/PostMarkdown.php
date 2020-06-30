<?php

namespace v5\Models\PostValues;

class PostMarkdown extends PostValue
{
    public $table = 'post_markdown';

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
            'value' => 'string'
        ];
        return [parent::getRules(), $rules];
    }//end getRules()
}//end class
