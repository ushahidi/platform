<?php

namespace Ushahidi\Modules\V5\Models;

class Layer extends BaseModel
{
    // public static $relationships = [
    //     'contact'
    // ];
    # --> and relationship to Post?

    //  const CREATED_AT = 'created';
    //  const UPDATED_AT = null;

    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
  //  protected $dateFormat = 'U';

    /**
     * Specify the table to load with Survey
     *
     * @var string
     */
    protected $table = 'layers';

    /**
     * Add relations to eager load
     *
     * @var string[]
     */
    protected $with = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var  array
     */
    protected $hidden = [];

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'media_id',
        'name',
        'type',
        'data_url',
        'options',
        'active',
        'visible_by_default',
        'created',
        'updated'
    ];

    public function setOptionsAttribute($value)
    {
        $this->attributes['options'] = $value ? json_encode($value) : null;
    }

    public function getOptionsAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }
}
