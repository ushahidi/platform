<?php

namespace v5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use v5\Models\BaseModel;

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
        return $this->hasOne('v5\Models\Media', 'id', 'media_id');
    }

    public function post()
    {
        return $this->hasOne('v5\Models\Post', 'id', 'post_id');
    }
    /**
     * Returns the attributes that can be translated for this model
     * @return string[]
     */
    public static function translatableAttributes():array
    {
        return [];
    }
}//end class
