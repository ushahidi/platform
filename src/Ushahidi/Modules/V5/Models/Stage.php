<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Modules\V5\Models\Scopes\StageAllowed;

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
    protected $casts = [
        'show_when_published' => 'boolean',
        'task_is_internal_only' => 'boolean',
    ];
    public function fields()
    {
        return $this->hasMany('Ushahidi\Modules\V5\Models\Attribute', 'form_stage_id');
    }

    public function survey()
    {
        return $this->belongsTo('Ushahidi\Modules\V5\Models\Survey', 'form_id');
    }

    /**
     * Get the stage's translation.
     */
    public function translations()
    {
        return $this->morphMany('Ushahidi\Modules\V5\Models\Translation', 'translatable');
    }
}
