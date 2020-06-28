<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use v4\Models\Helpers\HideTime;

class PostDatetime extends PostValue
{
    public $table = 'post_datetime';

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
            'value' => ['date'],
        ];
        return [parent::getRules(), $rules];
    }//end getRules()

    /**
     * @return bool
     */
    public function getValueAttribute($value)
    {
        return HideTime::hideTime($value, $this->post->survey ? $this->post->survey->hide_time : true);
    }
    public function setValueAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['value'] =  date("Y-m-d H:i:s", strtotime($value));
        }
    }
}//end class
