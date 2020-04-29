<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
class Survey extends Model
{
    protected $table = 'forms';
    protected $with = ['stages'];

    /**
     * The attributes that should be hidden for serialization.
     * @note this should be changed so that we either use the fractal transformer
     * OR a policy authorizer which is a more or less accepted method to do it 
     * (which uses the same $hidden type thing but it's much nicer obviously)
     *
     * @var array
     */
    protected $hidden = ['description'];
    /**
     * The attributes that should be mutated to dates.
     * @var array
    */
    protected $dates = ['created', 'updated'];

    /**
    * The attributes that are mass assignable.
    * @var array
    */
    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'type',
        'disabled',
        'require_approval',
        'everyone_can_create',
        'color',
        'hide_author',
        'hide_time',
        'hide_location',
        'targeted_survey'
    ];

    public function stages()
    {
        return $this->hasMany('v4\Models\Stage', 'form_id');
    }

}
