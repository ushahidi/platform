<?php

namespace Ushahidi\App\V5\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends BaseModel
{
    public $timestamps = false;

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
        'key',
        'label',
        'instructions',
        'input',
        'type',
        'required',
        'default',
        'priority',
        'options',
        'cardinality',
        'config',
        'response_private',
        'form_stage_id'
    ];
    /*
     * Query optimizations 2021.02.09:
     *   translations are generally only needed when rendering the response.
     *   Thus, it seems more adequate to ensure these are loaded by calling
     *   load() or loadMissing() from Resource::toArray().
     *   Doing this has resulted in far less queries when rendering JSON.
     */
    // protected $with = ['translations'];

    protected $casts = [
        'config' => 'json',
        'options' => 'json',
    ];
    protected $hidden = ['icon'];

    public function stage()
    {
        return $this->belongsTo('Ushahidi\App\V5\Models\Stage', 'form_stage_id');
    }

    public function getOptionsAttribute($value)
    {
        if ($this->type === 'tags') {
            $values = array_map(function ($v) {
                if (is_object($v)) {
                    return $v->id;
                }
                return $v;
            }, json_decode($value));
            return Category::whereIn('id', $values)->with(['parent', 'children', 'translations'])->get();
        }
        return json_decode($value);
    }
    /**
     * Get the attribute's translation.
     */
    public function translations()
    {
        return $this->morphMany('Ushahidi\App\V5\Models\Translation', 'translatable');
    }
}
