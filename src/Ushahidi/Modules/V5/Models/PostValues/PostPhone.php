<?php

namespace Ushahidi\Modules\V5\Models\PostValues;

class PostPhone extends PostValue
{
    public $table = 'post_phone';

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
            'value' => ['string', 'max:32'],
        ];
        return array_merge(parent::getRules(), $rules);
    }//end getRules()

    /**
     * @return bool
     */
    public function getValueAttribute($value)
    {
        return $value;
    }
}//end class
