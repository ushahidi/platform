<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Core\Entity\Permission;

class Stage extends Model
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

    public function fields()
    {
        return $this->hasMany('v4\Models\Attribute', 'form_stage_id');
    }

    public function survey()
    {
        return $this->belongsTo('v4\Models\Survey', 'form_id');
    }

    /**
     * Get the stage's translation.
     */
    public function translations()
    {
        return $this->morphMany('v4\Models\Translation', 'translatable');
    }
}
