<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{

    protected $table = 'form_attributes';
    /**
     * The attributes that should be mutated to dates.
     * @var array
    */
    protected $dates = ['created', 'updated'];

    /**
    * The attributes that are mass assignable.
    *
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

    public function stage () {
        return $this->belongsTo('v4\Models\Stage', 'form_stage_id');
    }

}
