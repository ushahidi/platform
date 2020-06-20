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
            'value' => ['date'],
        ];
        return [parent::getRules(), $rules];
    }//end getRules()

    /**
     * @return bool
     */
    public function getValueAttribute($value)
    {
        if (!$this->post->survey->hide_time) {
            return $value;
        }
        return HideTime::hideTime($value);
    }
}//end class
