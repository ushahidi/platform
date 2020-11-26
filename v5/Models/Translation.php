<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Core\Entity\Permission;

class Translation extends BaseModel
{
    public $timestamps = false;

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
        'translatable_id',
        'translated_key',
        'translatable_type',
        'translation',
        'language',
    ];

    /**
     * Get the owning translatable model.
     */
    public function translatable()
    {
        return $this->morphTo();
    }

    /**
     * Returns the attributes that can be translated for this model
     * @return string[]
     */
    public static function translatableAttributes():array
    {
        return [];
    }
}
