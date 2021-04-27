<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Ushahidi\App\Repository\FormRepository;
use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;

class Survey extends BaseModel
{
    use InteractsWithFormPermissions;
    public static $relationships = ['tasks', 'translations', 'enabled_languages'];
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
    protected $table = 'forms';

    /**
     * Add relations to eager load
     *
     * @var string[]
     */
    protected $with = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created',
        'updated',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'type',
        'disabled',
        'require_approval',
        'everyone_can_create',
        'color',
        'hide_author',
        'hide_time',
        'hide_location',
        'targeted_survey',
        'base_language',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['can_create'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type'                => 'report',
        'require_approval'    => true,
        'everyone_can_create' => true,
        'hide_author'         => false,
        'hide_time'           => false,
        'disabled'            => false,
        'hide_location'       => false,
        'targeted_survey'     => false,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'everyone_can_create' => 'boolean',
        'hide_author'         => 'boolean',
        'hide_time'           => 'boolean',
        'hide_location'       => 'boolean',
        'targeted_survey'     => 'boolean',
        'require_approval'    => 'boolean',
        'disabled'            => 'boolean',
    ];


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [
            'name.required'                             => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
            'name.min'                                  => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field'  => trans('fields.name'),
                ]
            ),
            'name.max'                                  => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field'  => trans('fields.name'),
                ]
            ),
            'name.regex'                                => trans(
                'validation.regex',
                ['field' => trans('fields.name')]
            ),
            // @TODO Add description.string
            // @TODO Add color.string
            'disabled.boolean'                          => trans(
                'validation.regex',
                ['field' => trans('fields.disabled')]
            ),
            'everyone_can_create.boolean'               => trans(
                'validation.regex',
                ['field' => trans('fields.everyone_can_create')]
            ),
            'hide_author.boolean'                       => trans(
                'validation.regex',
                ['field' => trans('fields.hide_author')]
            ),
            'hide_location.boolean'                     => trans(
                'validation.regex',
                ['field' => trans('fields.hide_location')]
            ),
            'hide_time.boolean'                         => trans(
                'validation.regex',
                ['field' => trans('fields.hide_time')]
            ),
            'targeted_survey.boolean'                   => trans(
                'validation.regex',
                ['field' => trans('fields.targeted_survey')]
            ),
            'tasks.*.label.required'                    => trans(
                'validation.not_empty',
                ['field' => trans('fields.tasks.label')]
            ),
            'tasks.*.label.boolean'                     => trans(
                'validation.regex',
                ['field' => trans('fields.tasks.label')]
            ),
            'tasks.*.type.in'                           => trans(
                'validation.in_array',
                ['field' => trans('fields.tasks.type')]
            ),
            'tasks.*.priority.numeric'                  => trans(
                'validation.numeric',
                ['field' => trans('fields.tasks.priority')]
            ),
            'tasks.*.icon.alpha'                        => trans(
                'validation.alpha',
                ['field' => trans('fields.tasks.icon')]
            ),
            'tasks.*.fields.*.label.required'           => trans(
                'validation.not_empty',
                ['field' => trans('fields.tasks.fields.label')]
            ),
            'tasks.*.fields.*.label.max'                => trans(
                'validation.max_length',
                [
                    'param2' => 150,
                    'field'  => trans('fields.tasks.fields.label'),
                ]
            ),
            'tasks.*.fields.*.key.alpha_dash'           => trans(
                'validation.alpha_dash',
                ['field' => trans('fields.tasks.fields.key')]
            ),
            'tasks.*.fields.*.key.max'                  => trans(
                'validation.max_length',
                [
                    'param2' => 150,
                    'field'  => trans('fields.tasks.fields.key'),
                ]
            ),
            'tasks.*.fields.*.input.required'           => trans(
                'validation.not_empty',
                ['param2' => trans('fields.tasks.fields.input')]
            ),
            'tasks.*.fields.*.input.in'                 => trans(
                'validation.in_array',
                ['field' => trans('fields.tasks.fields.input')]
            ),
            'tasks.*.fields.*.type.required'            => trans(
                'validation.not_empty',
                ['field' => trans('fields.tasks.fields.type')]
            ),
            'tasks.*.fields.*.type.in'                  => trans(
                'validation.in_array',
                ['field' => trans('fields.tasks.fields.type')]
            ),
            'tasks.*.fields.*.priority.numeric'         => trans(
                'validation.numeric',
                ['field' => trans('fields.tasks.fields.priority')]
            ),
            'tasks.*.fields.*.cardinality.numeric'      => trans(
                'validation.numeric',
                ['field' => trans('fields.tasks.fields.cardinality')]
            ),
            'tasks.*.fields.*.response_private.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.tasks.fields.response_private')]
            ),
            // 'tasks.*.fields.*.response_private' => [
            // @TODO add this custom validator for canMakePrivate
            // [[$this, 'canMakePrivate'], [':value', $type]]
            // ]
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
            'name'                              => [
                'required',
                'min:2',
                'max:255',
                'regex:'.LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'description'                       => [
                'string',
                'nullable',
            ],
            'color'                             => [
                'string',
                'nullable',
            ],
            'disabled'                          => ['boolean'],
            'everyone_can_create'               => ['boolean'],
            'hide_author'                       => ['boolean'],
            'hide_location'                     => ['boolean'],
            'hide_time'                         => ['boolean'],
            'targeted_survey'                   => ['boolean'],
            'tasks.*.label'                     => [
                'required',
                'regex:'.LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'tasks.*.type'                      => [Rule::in(
                [
                    'post',
                    'task',
                ]
            )],
            'tasks.*.priority'                  => ['numeric'],
            'tasks.*.icon'                      => ['alpha'],
            'tasks.*.fields.*.label'            => [
                'required',
                'max:150',
            ],
            'tasks.*.fields.*.key'              => [
                'max:150',
                'alpha_dash',
                // @TODO: add this validation for keys
                // [[$this->repo, 'isKeyAvailable'], [':value']]
            ],
            'tasks.*.fields.*.input'            => [
                'required',
                Rule::in(
                    [
                        'text',
                        'textarea',
                        'select',
                        'radio',
                        'checkbox',
                        'checkboxes',
                        'date',
                        'datetime',
                        'location',
                        'number',
                        'relation',
                        'upload',
                        'video',
                        'markdown',
                        'tags',
                    ]
                ),
            ],
            'tasks.*.fields.*.type'             => [
                'required',
                Rule::in(
                    [
                        'decimal',
                        'int',
                        'geometry',
                        'text',
                        'varchar',
                        'markdown',
                        'point',
                        'datetime',
                        'link',
                        'relation',
                        'media',
                        'title',
                        'description',
                        'tags',
                    ]
                )
            ],
            'tasks.*.fields.*.type'             => ['string'],
            'tasks.*.fields.*.priority'         => ['numeric'],
            'tasks.*.fields.*.cardinality'      => ['numeric'],
            'tasks.*.fields.*.response_private' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    $type_field = Arr::get(Request::input(), str_replace('response_private', 'type', $attribute));
                    if ($type_field === 'tags' && $value != false) {
                        return $fail(trans('validation.tag_field_type_cannot_be_private'));
                    }
                }
            ],
            // @NOTE: checkPostTypeLimit is not used here.
            // Before merge, validate with Angela if we
            // should be removing that arbitrary limit since it's pretty rare
            // for it to be needed
        ];
    }//end getRules()

    public function canMakePrivate($value, $type)
    {
        // If input type is tags, then attribute cannot be private
        if ($type === 'tags' && $value !== false) {
            return false;
        }

        return true;
    }

    /**
     * Returns survey required tasks that are NOT in the provided set
     * of complete tasks
     */
    public function getMissingRequiredTasks($complete_tasks)
    {
        // TODO: write logic and enable proper tests
        return [];
    }

    /**
     * This is what makes can_create possible
     *
     * @return mixed
     */
    public function getCanCreateAttribute()
    {
        $can_create = $this->getCanCreateRoles($this->id);
        return $can_create['roles'];
    }//end getCanCreateAttribute()


    private function getCanCreateRoles($form_id)
    {
        /*
         * @NOTE: to lower changes of a regression I'm using some helpers from
         * repositories and traits we already have
         * @NOTE: during origami and later stages of sunny buffers, we will fold this
         * all together in more performant and friendly ways
         */
        $form_repo = service('repository.form');
        return $form_repo->getRolesThatCanCreatePosts($form_id);
    }//end getCanCreateRoles()


    /**
     * We check for relationship permissions here, to avoid hydrating anything that should not be hydrated.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        $authorizer = service('authorizer.form');
        $user       = $authorizer->getUser();
        // NOTE: this acl->hasPermission check is all `canUserEditForm` does, so we're doing that directly
        // to avoid an hydration issue with InteractsWithFormPermissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            // if this permission is set we can go ahead and hydrate all the stages
            return $this->hasMany('v5\Models\Stage', 'form_id');
        }

        return $this->hasMany(
            'v5\Models\Stage',
            'form_id'
        )
        ->where('form_stages.show_when_published', '=', '1')
        ->where('form_stages.task_is_internal_only', '=', '0');
    }//end tasks()


    /**
     * Get the survey's translation.
     */
    public function translations()
    {
        return $this->morphMany('v5\Models\Translation', 'translatable');
    }//end translations()

    /**
     * Get the survey color.
     *
     * @param  string  $value
     * @return void
     */
    public function getColorAttribute($value)
    {
        return $value ? "#" . $value : $value;
    }
    /**
     * Set the survey color
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
}//end class
