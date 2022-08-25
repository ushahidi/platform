<?php

namespace Ushahidi\Modules\V5\Models\PostValues;

use Ushahidi\Modules\V5\Models\BaseModel;
use Ushahidi\Modules\V5\Models\Translation;
use Ushahidi\Modules\V5\Models\Helpers\HideTime;

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
        return $this->morphMany(Translation::class, 'translatable', null, 'translatable_id', 'id');
    }//end translations()

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
        return $this->hasOne('Ushahidi\Modules\V5\Models\Attribute', 'id', 'form_attribute_id');
    }

    public function post()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Post\Post', 'id', 'post_id');
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
