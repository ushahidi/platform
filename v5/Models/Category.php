<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Core\Entity\Permission;
use Illuminate\Support\Facades\Request;
use v5\Models\Scopes\CategoryAllowed;

class Category extends BaseModel
{
    public $errors;
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

    /**
     * Add relations to eager load
     *
     * @var string[]
     */
    /*
     * Query optimizations 2021.02.09:
     *   translations are generally only needed when rendering the response.
     *   Thus, it seems more adequate to ensure these are loaded by calling
     *   load() or loadMissing() from Resource::toArray().
     *   Doing this has resulted in far less queries when rendering JSON.
     */
    // protected $with = ['translations'];
    protected $translations;
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [
            'parent_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.parent_id')]
            ),
            'tag.required'      => trans(
                'validation.not_empty',
                ['field' => trans('fields.tag')]
            ),
            'tag.unique'      => trans(
                'validation.unique',
                ['field' => trans('fields.tag')]
            ),
            'tag.min'           => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field'  => trans('fields.tag'),
                ]
            ),
            'tag.max'           => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field'  => trans('fields.tag'),
                ]
            ),
            'tag.regex'         => trans(
                'validation.regex',
                ['field' => trans('fields.tag')]
            ),
            'slug.required'     => trans(
                'validation.not_empty',
                ['field' => trans('fields.slug')]
            ),
            'slug.min'          => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field'  => trans('fields.slug'),
                ]
            ),
            'slug.unique'      => trans(
                'validation.unique',
                ['field' => trans('fields.slug')]
            ),
            'type.required'     => trans(
                'validation.not_empty',
                ['field' => trans('fields.type')]
            ),
            'type.in'           => trans(
                'validation.in_array',
                ['field' => trans('fields.type')]
            ),
            'description.regex' => trans(
                'validation.regex',
                ['field' => trans('fields.description')]
            ),

            'description.min' => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field' => trans('fields.description')
                ]
            ),

            'description.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.description')
                ]
            ),
            'icon.regex'        => trans(
                'validation.regex',
                ['field' => trans('fields.icon')]
            ),
            'priority.numeric'  => trans(
                'validation.numeric',
                ['field' => trans('fields.priority')]
            ),
        ];
    }//end translations()

    /**
     * Return all validation rules
     * @param null $data
     * @return array
     */
    public function getRules($data = null)
    {
        $id = isset($data['id']) ? $data['id'] : $this->id;
        $parent_id = isset($data['parent_id']) ? ($data['parent_id']) : $this->parent_id;
        return [
             'parent_id' => 'nullable|sometimes|exists:tags,id',
             'tag'         => [
                'required',
                'min:2',
                'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD',
                Rule::unique('tags', 'tag')->ignore($id)
             ],
             'slug'        => [
                'required',
                'min:2',
                Rule::unique('tags', 'slug')->ignore($id)
             ],
             'type'        => [
                'required',
                Rule::in([
                    'category',
                    'status'
                ])
             ],
             'description' => [
                 'nullable',
                 'min:2',
                 'max:255'
             ],
             'color'                             => [
                 'string',
                 'nullable',
             ],
             'icon'        => [
                'regex:/^[\pL\s\_\-]++$/uD'
             ],
             'priority'    => [
                'numeric'
             ],
             'role' => [
                function ($attribute, $value, $fail) use ($parent_id) {
                    $parent = $parent_id ? Category::find($parent_id) : null;
                    // ... and check if the role matches its parent
                    if ($parent && $parent->role != $value) {
                        return $fail(trans('validation.child_parent_role_match'));
                    }
                    if (is_array($value) && empty($value)) {
                        return $fail(trans('validation.role_cannot_be_empty'));
                    }
                }
             ]
        ];
    }//end validationMessages()

    /**
     * Get the category's translation.
     */
    public function translations()
    {
        return $this->morphMany('v5\Models\Translation', 'translatable');
    }//end getRules()

    public function parent()
    {
        return $this->hasOne('v5\Models\Category', 'id', 'parent_id');
    }
    public function children()
    {
        return $this->hasMany('v5\Models\Category', 'parent_id', 'id')->withoutGlobalScopes();
    }


    /**
     * Get the category's color format
     *
     * @param  string  $value
     * @return void
     */
    public function getColorAttribute($value)
    {
        return $value ? "#" . $value : $value;
    }
    /**
     * Set the category's color format
     *
     * @param  string  $value
     * @return void
     */
    public function setColorAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['color'] = ltrim($value, '#');
        }
    }

    public function validate($data = [])
    {
        $v = Validator::make($data, $this->getRules($data), $this->validationMessages());

        $v->sometimes('role', 'exists:roles,name', function ($input) {
            return !!$input->get('role');
        });
        // check for failure
        if (!$v->fails()) {
            return true;
        }
        // set errors and return false
        $this->errors = $v->errors();
        return false;
    }

    public function errors()
    {
        return $this->errors;
    }
}//end class
