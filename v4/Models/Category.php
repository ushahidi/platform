<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Ushahidi\Core\Entity\Permission;

class Category extends Model
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
     * @note this should be changed so that we either use the fractal transformer
     * OR a policy authorizer which is a more or less accepted method to do it
     * (which uses the same $hidden type thing but it's much nicer obviously)
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
        'priority'
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
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function validationMessages()
    {
        return [
            'tag.required'      => trans(
                'validation.not_empty',
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
     * @return array
     */
    protected static function getRules()
    {
        return [
            // 'parent_id' = [
            // [[$this->repo, 'doesTagExist'], [':value']],
            // ]
            'tag'         => [
                'required',
                'min:2',
                'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD'
            ],
            'slug'        => [
                'required',
                'min:2',
                // [[$this->repo, 'isSlugAvailable'], [':value']],
                // 'min:2',
                // 'max:255',
                // 'regex:'.LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'type'        => [
                'required',
                Rule::in([
                    'category',
                    'status'
                ])
                // 'min:2',
                // 'max:255',
                // 'regex:'.LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'description' => [
                'regex:/^[\pL\pN\pP ]++$/uD'
                // 'min:2',
                // 'max:255',
                // 'regex:'.LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            // 'color' => [
            // ['color'],
            // ]
            'icon'        => [
                'regex:/^[\pL\s\_\-]++$/uD'
            ],
            'priority'    => [
                'numeric'
            ],
            // 'role' => [
            // [[$this->role_repo, 'exists'], [':value']],
            // [[$this->repo, 'isRoleValid'], [':validation', ':fulldata']]
            // ]
        ];
    }//end validationMessages()

    /**
     * Get the category's translation.
     */
    public function translations()
    {
        return $this->morphMany('v4\Models\Translation', 'translatable');
    }//end getRules()

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        /**
         * If no roles are selected, the Tag is considered
         * completely public.
         */
        $authorizer = service('authorizer.tag');
        $user = $authorizer->getUser();

        if ($user->role) {
            // couldn't think of a better way to deal with our JSON-but-not-json fields
            return $query->where(function ($query) use ($user) {
                return $query
                    ->whereNull('role')
                    ->orWhere('role', 'LIKE', '%\"' . $user->role . '\"%');
            });
        }
        return $query->whereNull('role');
    }
}//end class
