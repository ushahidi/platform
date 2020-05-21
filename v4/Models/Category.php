<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Core\Entity\Permission;

class Category extends Model
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
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function validationMessages()
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
    public function getRules()
    {
        return [
             'parent_id' => 'sometimes|exists:tags,id',
             'tag'         => [
                'required',
                'min:2',
                'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD',
                Rule::unique('tags')->ignore($this->id)
             ],
             'slug'        => [
                'required',
                'min:2',
                Rule::unique('tags')->ignore($this->id)
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

    public function parent()
    {
        return $this->hasOne('v4\Models\Category', 'id', 'parent_id');
    }
    public function children()
    {
        return $this->hasMany('v4\Models\Category', 'parent_id', 'id');
    }

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
            // get categories that are available for users with this role or NULL role
            // taking care NOT to bring any child categories that belong
            // to parents with other role restrictions
            $q = $query->where(function ($query) use ($user) {
                return $query
                    ->whereNull('role')
                    ->orWhere('role', 'LIKE', '%\"' . $user->role . '\"%')
                    ;
            });
            $q->where(function ($query) use ($user) {
                return $query
                    ->whereNotIn('parent_id', function ($query) use ($user) {
                        $query
                            ->select('id')
                            ->from('tags')
                            ->where('role', 'NOT LIKE', '%\"' . $user->role . '\"%')
                            ->whereNull('parent_id');
                    })
                    ->orWhereNull('parent_id');
            });
            // generates a query like like this
            // select * from `tags` where (`role` is null or `role` LIKE ?)
            // AND (`parent_id` not in
            // (
            //  select `id` from `tags` where `role` NOT LIKE ? and `parent_id` is null
            // )
            // or `parent_id` is null)
            return $q;
        }
        // get categories that are available for non logged in users
        // taking care NOT to bring any child categories that belong
        // to parents with admin/user/other role restrictions
        $q = $query->whereNull('role')->where(function ($query) use ($user) {
            return $query
                ->whereNotIn('parent_id', function ($query) use ($user) {
                    $query
                        ->select('id')
                        ->from('tags')
                        ->whereNotNull('role')
                        ->whereNull('parent_id');
                })
                ->orWhereNull('parent_id');
        });
        // generates a query like this:
        // select * from `tags` where `role` is null
        // AND (`parent_id` not in
        // (
        //  select `id` from `tags` where `role` is not null and `parent_id` is null
        // )
        // or `parent_id` is null)

        return $q;
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

    /**
     * Get the category's slug
     *
     * @param  string  $value
     * @return void
     */
    public function getSlugAttribute($value)
    {
        return $value;
    }
    /**
     * Set the category's slug format
     *
     * @param  string  $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        if (isset($value) && (!isset($this->attributes['slug']))) {
            $value = self::makeSlug($value);
            $this->attributes['slug'] = $value;
        }
    }
    public static function makeSlug($value)
    {
        // Make it lowercase
        $value = mb_strtolower($value, 'utf-8');

        // .. anything not the separator, letters, numbers or whitespace is replaced
        $value = preg_replace('/[^\pL\pN\-\s]+/u', '', $value);

        // .. replace whitespace and multiple separator chars with a single separator
        $value = preg_replace('/[\-\s]+/u', '-', $value);

        // ... and replace spaces with hypens
        $value = str_replace(' ', '-', $value);
        return $value;
    }

    public function validate($data)
    {
        $v = Validator::make($data, $this->getRules(), self::validationMessages());
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
