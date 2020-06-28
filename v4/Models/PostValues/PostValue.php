<?php

namespace v4\Models\PostValues;

use v4\Models\BaseModel;
use v4\Models\Helpers\HideTime;
use v4\Models\Scopes\PostValueAllowed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PostValue extends BaseModel
{
    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
    public $with = ['translations'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created',
    ];

    /**
     * @var array
    */
    protected $fillable = [
        'post_id',
        'form_attribute_id',
        'value',
    ];

    /**
     * Get the post value's translation.
     */
    public function translations()
    {
        return $this->morphMany('v4\Models\Translation', 'translatable', null, 'translatable_id', 'id');
    }//end translations()

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PostValueAllowed);
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
        return [
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'form_attribute_id' => 'nullable|sometimes|exists:form_attributes,id',
        ];
    }//end getRules()

    public function attribute()
    {
        return $this->hasOne('v4\Models\Attribute', 'id', 'form_attribute_id');
    }

    public function post()
    {
        return $this->hasOne('v4\Models\Post', 'id', 'post_id');
    }

    /**
     * @return bool
     */
    public function getUpdatedAttribute($value)
    {
        return HideTime::hideTime($value, $this->post->survey ? $this->post->survey->hide_time : true);
    }
    /**
     * @return bool
     */
    public function getCreatedAttribute($value)
    {
        return HideTime::hideTime($value, $this->post->survey ? $this->post->survey->hide_time : true);
    }
}//end class
