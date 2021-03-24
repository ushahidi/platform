<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Lock extends BaseModel
{

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
        'expires',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'expires'
    ];

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        return $query;
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
            'post_id' => 'exists:posts,id',
            'user_id' => 'exists:users,id',
            'expires' => 'date',
        ];
    }//end getRules()

    public function post()
    {
        return $this->hasOne('v5\Models\Post\Post', 'id', 'post_id');
    }
}//end class
