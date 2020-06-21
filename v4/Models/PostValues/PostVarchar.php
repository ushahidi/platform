<?php

namespace v4\Models\PostValues;

class PostVarchar extends PostValue
{
    public $table = 'post_varchar';

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
            'value' => ['string', 'max:255'],
        ];
        return [parent::getRules(), $rules];
    }//end getRules()

    /**
     * @return bool
     */
    public function getValueAttribute($value)
    {
        return $value;
    }

    public function setValueAttribute($value)
    {
        if (isset($value) && $this->attribute->input === 'checkbox') {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}//end class
