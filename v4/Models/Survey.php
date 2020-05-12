<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Ushahidi\App\Repository\FormRepository;
use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;

class Survey extends Model
{
    use InteractsWithFormPermissions;

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
    protected $with = ['tasks'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var  array
     * @note this should be changed so that we either use the fractal transformer
     * OR a policy authorizer which is a more or less accepted method to do it
     * (which uses the same $hidden type thing but it's much nicer obviously)
     */
    protected $hidden = ['description'];

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
        'require_approval'    => 'boolean',
        'disabled'            => 'boolean',
    ];


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function validationMessages()
    {
        return [
            'name.required'                             => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
            'name.min'                                  => trans(
                'validation.min_length',
                ['param2' => 2]
            ),
            'name.max'                                  => trans(
                'validation.max_length',
                ['param2' => 255]
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
                ['param2' => trans('fields.tasks.fields.label')]
            ),
            'tasks.*.fields.*.key.alpha_dash'           => trans(
                'validation.alpha_dash',
                ['field' => trans('fields.tasks.fields.key')]
            ),
            'tasks.*.fields.*.key.max'                  => trans(
                'validation.max_length',
                ['param2' => trans('fields.tasks.fields.key')]
            ),
            'tasks.*.fields.*.input.required'           => trans(
                'validation.not_empty',
                ['param2' => trans('fields.tasks.fields.input')]
            ),
            'tasks.*.fields.*.input.in'                 => trans(
                'validation.in_array',
                ['param2' => trans('fields.tasks.fields.input')]
            ),
            'tasks.*.fields.*.type.required'            => trans(
                'validation.not_empty',
                ['param2' => trans('fields.tasks.fields.type')]
            ),
            'tasks.*.fields.*.type.in'                  => trans(
                'validation.in_array',
                ['param2' => trans('fields.tasks.fields.type')]
            ),
            'tasks.*.fields.*.priority.numeric'         => trans(
                'validation.numeric',
                ['param2' => trans('fields.tasks.fields.priority')]
            ),
            'tasks.*.fields.*.cardinality.numeric'      => trans(
                'validation.numeric',
                ['param2' => trans('fields.tasks.fields.cardinality')]
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
    protected static function getRules()
    {
        return [
            'name'                              => [
                'required',
                'min:2',
                'max:255',
                'regex:' . LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'description'                       => [
                'string',
                'nullable',
            ],
            // @TODO find out where this color validator is implemented
            // [['color']],
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
                'regex:' . LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'tasks.*.type'                      => [
                Rule::in(
                    [
                        'post',
                        'task',
                    ]
                )
            ],
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
                ),
                // @TODO: add this validation for duplicates in type?
                // [[$this, 'checkForDuplicates'], [':validation', ':value']],
            ],
            'tasks.*.fields.*.type'             => ['string'],
            'tasks.*.fields.*.priority'         => ['numeric'],
            'tasks.*.fields.*.cardinality'      => ['numeric'],
            'tasks.*.fields.*.response_private' => [
                'boolean'
                // @TODO add this custom validator for canMakePrivate
                // [[$this, 'canMakePrivate'], [':value', $type]]
            ],
            // @NOTE: checkPostTypeLimit is not used here.
            // Before merge, validate with Angela if we
            // should be removing that arbitrary limit since it's pretty rare
            // for it to be needed
        ];
    }//end getRules()


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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        $authorizer = service('authorizer.form');
        $user = $authorizer->getUser();
        // NOTE: this acl->hasPermission check is all `canUserEditForm` does, so we're doing that directly
        // to avoid an hydration issue with InteractsWithFormPermissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            // if this permission is set we can go ahead and hydrate all the stages
            return $this->hasMany('v4\Models\Stage', 'form_id');
        }

        return $this->hasMany(
            'v4\Models\Stage',
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
        return $this->morphMany('v4\Models\Translation', 'translatable');
    }//end translations()
}//end class
