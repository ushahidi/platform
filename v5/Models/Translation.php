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
        'translatable_id', // the id of the object we are translating
        'translated_key', // the database key we are translating, like `label` or `value`
        'translatable_type', // the object type. This has to be a relationship name. Example: post_value_markdown.
        'translation', // the translation itself
        'language', // the language we are translating to
    ];

    /**
     * Get the owning translatable model.
     */
    public function translatable()
    {
        return $this->morphTo();
    }
}
