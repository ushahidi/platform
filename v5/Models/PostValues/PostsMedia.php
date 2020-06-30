<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use v4\Models\BaseModel;

class PostsMedia extends BaseModel
{
    public $table = 'posts_media';
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
        'media_id',
    ];

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
            'media_id' => 'nullable|sometimes|exists:media,id',
        ];
    }//end getRules()


    public function media()
    {
        return $this->hasOne('v4\Models\Media', 'id', 'media_id');
    }

    public function post()
    {
        return $this->hasOne('v4\Models\Post', 'id', 'post_id');
    }
}//end class
