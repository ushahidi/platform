<?php

namespace Ushahidi\Modules\V5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Helpers\HideTime;

class PostMedia extends PostValue
{
    public $table = 'post_media';

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
        'value'
    ];

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [];
    }

    /**
     * Return all validation rules
     *
     * @return array
     */
    public function getRules()
    {
        return [
            'post_id' => 'required|exists:posts,id',
            'value' => 'required|exists:media,id',
            'form_attribute_id' => 'required|exists:form_attribute,id'
        ];
    }
    public function attribute()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Attribute', 'id', 'form_attribute_id');
    }

    public function media()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Media', 'id', 'value');
    }

    public function post()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Post\Post', 'id', 'post_id');
    }

    public function getCreatedAttribute($value)
    {
        $time = HideTime::hideTime($value, $this->survey ? $this->survey->hide_time : true);
        return self::makeDate($time);
    }
}
