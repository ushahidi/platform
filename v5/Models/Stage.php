<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Core\Entity\Permission;
use v5\Models\Scopes\StageAllowed;

class Stage extends BaseModel
{
    public $timestamps = false;

    protected $table = 'form_stages';
    /**
     * The attributes that should be mutated to dates.
     * @var array
    */
    protected $dates = ['created', 'updated'];
    protected $with = ['fields', 'translations'];
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'form_id',
        'label',
        'priority',
        'icon',
        'required',
        'type',
        'description',
        'show_when_published',
        'task_is_internal_only'
    ];
    protected $hidden = ['icon'];

    public function fields()
    {
        return $this->hasMany('v5\Models\Attribute', 'form_stage_id');
    }

    public function survey()
    {
        return $this->belongsTo('v5\Models\Survey', 'form_id');
    }

    /**
     * Get the stage's translation.
     */
    public function translations()
    {
        return $this->morphMany('v5\Models\Translation', 'translatable');
    }
}
