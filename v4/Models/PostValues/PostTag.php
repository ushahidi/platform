<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use v4\Models\Helpers\HideTime;

class PostTag extends Model
{
    public $table = 'posts_tags';
    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

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
        'tag_id',
        'form_attribute_id'
    ];

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
        return [
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'tag_id' => 'nullable|sometimes|exists:tags,id',
            'form_attribute_id' => 'nullable|sometimes|exists:form_attributes,id'
        ];
    }//end getRules()

    public function validate($data)
    {
        $v = Validator::make($data, $this->getRules(), self::validationMessages());
        // check for failure
        if (!$v->fails()) {
            return true;
        }
        // set errors and return false
        $this->errors = $v->errors();
        return false;
    }

    public function attribute()
    {
        return $this->hasOne('v4\Models\Attribute', 'id', 'form_attribute_id');
    }

    public function tag()
    {
        return $this->hasOne('v4\Models\Category', 'id', 'tag_id');
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
