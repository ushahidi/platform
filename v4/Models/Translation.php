<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Core\Entity\Permission;

class Translation extends Model
{
    public $timestamps = FALSE;

    protected $table = 'translations';
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
        'entity_type',
        'entity_id',
        'translated_key',
        'translation',
        'language',
    ];

    /**
     * Get the owning imageable model.
     */
    public function translatable()
    {
        return $this->morphTo();
    }
}
