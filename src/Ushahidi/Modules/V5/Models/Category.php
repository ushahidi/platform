<?php

namespace Ushahidi\Modules\V5\Models;

class Category extends BaseModel
{
    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Specify the table to load with Survey
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var  array
     */
    protected $hidden = [
        'description',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'user_id',
        'tag',
        'slug',
        'type',
        'color',
        'icon',
        'description',
        'role',
        'priority',
        'base_language'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'category'
    ];

    protected $casts = [
        'role' => 'json'
    ];

    // protected $with = ['translations'];

    /**
     * Get the category's translation.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function parent()
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->withoutGlobalScopes();
    }

    /**
     * Get the category's color format
     */
    public function getColorAttribute($value)
    {
        return $value ? "#" . $value : $value;
    }

    /**
     * Set the category's color format
     */
    public function setColorAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['color'] = ltrim($value, '#');
        }
    }

    // public function setRoleAttribute($value)
    // {
    //     $this->attributes['role'] = $value ? json_encode($value) : null;
    // }

    // public function getRoleAttribute($value)
    // {
    //     return $value ? json_decode($value, true) : null;
    // }
}
