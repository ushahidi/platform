<?php

namespace v5\Models\PostValues;

use v5\Models\Helpers\HideTime;

class PostDatetime extends PostValue
{
    public $table = 'post_datetime';

    /**
     * @inheritdoc
    */
    protected $fillable = [
        'post_id',
        'form_attribute_id',
        'value',
        'metadata',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'value' => 'date',
    ];

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
            'value' => ['date'],
        ];

        return array_merge(parent::getRules(), $rules);
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
