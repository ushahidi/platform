<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ushahidi\App\Repository\FormRepository;
use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;
use Ushahidi\Core\Tool\Permissions\InteractsWithPostPermissions;

class PostTag extends BaseModel
{
    use InteractsWithPostPermissions;

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
    protected $table = 'posts_tags';

    /**
     * Add relations to eager load
     *
     * @var string[]
     */
    protected $with = ['attribute', 'post'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var  array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created',
    ];

    /**
     * @var array
    */
    protected $fillable = [
        'post_id',
        'tag_id',
        'form_attribute_id',
    ];

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        return $query;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [
        ];
    }//end validationMessages()

    /**
     * Return all validation rules
     *
     * @return array
     */
    public function getRules()
    {
        return [
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'tag_id' => 'nullable|sometimes|exists:tags,id',
            'form_attribute_id' => 'nullable|sometimes|exists:form_attributes,id',
        ];
    }//end getRules()

    public function category()
    {
        return $this->hasOne('v5\Models\Category', 'id', 'tag_id');
    }

    public function attribute()
    {
        return $this->hasOne('v5\Models\Attribute', 'id', 'form_attribute_id');
    }

    public function post()
    {
        return $this->hasOne('v5\Models\Post\Post', 'id', 'post_id');
    }
}//end class
