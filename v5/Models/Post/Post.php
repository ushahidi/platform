<?php
/**
 * *
 *  * Ushahidi Acl
 *  *
 *  * @author     Ushahidi Team <team@ushahidi.com>
 *  * @package    Ushahidi\Application
 *  * @copyright  2020 Ushahidi
 *  * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 *
 */

namespace v5\Models\Post;

use v5\Models\BaseModel;
use v5\Models\Message;
use v5\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use v5\Models\Helpers\HideTime;
use v5\Models\Helpers\HideAuthor;
use v5\Models\Scopes\PostAllowed;
use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Ushahidi\App\Repository\FormRepository;
use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;
use Ushahidi\Core\Tool\Permissions\InteractsWithPostPermissions;

class Post extends BaseModel
{
    use InteractsWithPostPermissions;

    /**
     * This relationships aren't real, they are fabricated
     * with the intention of using them in Resource objects
     * Which is why you see 'post_content' rather than postValueVarchar
     * @var string[]
     */
    public static $relationships = [
        'survey',
        'locks',
        'categories',
        'comments',
        'message',
        'contact',
        'post_content',
        'completed_stages',
        'translations',
        'enabled_languages'
    ];
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
    protected $table = 'posts';

    /**
     * Add relations to eager load
     *
     * @var string[]
     */
    protected $with = ['message', 'translations'];
    protected $translations;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var  array
     */
    protected $hidden = [
    ];

    /**
     * @var array
    */
    protected $fillable = [
        'form_id',
        'user_id',
        'type',
        'title',
        'slug',
        'content',
        'author_email',
        'author_realname',
        'status',
        'published_to',
        'locale',
        'post_date',
        'base_language',
        'created',
        'updated'
    ];
    /**
     * The model's default values for attributes.
     *
     * @var array
    */
    protected $attributes = [
        'type'                => 'report',
        'locale'              => 'en_US',
        'published_to'        => '',
        'status'              => PostStatus::DRAFT
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
        'published_to'        => 'json'
    ];

    private function getBulkRules()
    {
        return [
            'items.*.id' => [
                'required',
                'integer',
                'exists:posts,id',
                'distinct'
            ]
        ];
    }

    public function getBulkPatchRules()
    {
        return array_merge_recursive(
            $this->getBulkRules(),
            [
                'items.*.status' => [
                    'required',
                    'string',
                    Rule::in(PostStatus::all())
                ],
            ]
        );
    }

    public function getBulkDeleteRules()
    {
        return $this->getBulkRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [
            'form_id.exists'                             => trans(
                'validation.exists',
                ['field' => trans('fields.form_id')]
            ),
            'user_id.exists'                             => trans(
                'validation.exists',
                ['field' => trans('fields.user_id')]
            ),
            'type.required'                             => trans(
                'validation.required',
                ['field' => trans('fields.type')]
            ),
            'type.in'                             => trans(
                'validation.in_array',
                ['field' => trans('fields.type')]
            ),
            'title.required'                             => trans(
                'validation.required',
                ['field' => trans('fields.title')]
            ),
            'title.max'                             => trans(
                'validation.max',
                [
                    'param2' => 150,
                    'field'  => trans('fields.title'),
                ]
            ),
            'title.regex'                             => trans(
                'validation.regex',
                [
                    'field'  => trans('fields.title'),
                ]
            ),
            'slug.required'                             => trans(
                'validation.required',
                ['field' => trans('fields.slug')]
            ),
            'slug.min'                             => trans(
                'validation.min',
                [
                    'param2' => 2,
                    'field'  => trans('fields.slug'),
                ]
            ),
            'slug.unique'                             => trans(
                'validation.unique',
                [
                    'field'  => trans('fields.slug'),
                ]
            ),
            'content.string'                             => trans(
                'validation.string',
                [
                    'field'  => trans('fields.content'),
                ]
            )
        ];
    }//end validationMessages()

    /**
     * Get the error messages for the defined *bulk* validation rules.
     *
     * @return array
     */
    private function bulkValidationMessages()
    {
        return [
            'items.*.id.required'                 => trans(
                'validation.exists',
                ['field' => 'id']
            ),
            'items.*.id.integer'                  => trans(
                'validation.integer',
                ['field' => 'id']
            ),
            'items.*.id.exists'                      => trans(
                'validation.ref_exists',
                ['field' => 'id', 'model' => 'post']
            ),
            'items.*.id.distinct'                      => trans(
                'bulk.distinct',
                ['field' => 'id']
            ),
        ];
    }//end bulkValidationMessages()

    /**
     * Get the error messages for the defined *bulk* validation rules.
     *
     * @return array
     */
    public function bulkPatchValidationMessages()
    {
        return array_merge(
            $this->bulkValidationMessages(),
            [
                'items.*.status.required'                 => trans(
                    'validation.exists',
                    ['field' => 'status']
                ),
                'items.*.status.string'                  => trans(
                    'validation.string',
                    ['field' => 'status']
                ),
                'items.*.status.in'                      => trans(
                    'validation.in_array',
                    ['field' => 'id']
                )
            ]
        );
    }//end bulkValidationMessages()

    /**
     * Get the error messages for the defined *bulk* validation rules.
     *
     * @return array
     */
    public function bulkDeleteValidationMessages()
    {
        return $this->bulkValidationMessages();
    }

    /**
     * Return all validation rules
     *
     * @return array
     */
    public function getRules()
    {
        return [
            'form_id' => 'nullable|sometimes|exists:forms,id',
            'user_id' => 'nullable|sometimes|exists:users,id',
            'type'             => [
                'required',
                Rule::in(
                    [
                        'report',
                        'update',
                        'revision'
                    ]
                )
            ],
            'title'            => [
                'required',
                'max:150',
                'regex:'.LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'slug'        => [
                'required',
                'min:2',
                Rule::unique('posts')->ignore($this->id)
            ],
            'content' => [
                'string'
            ],
            'author_email' => 'nullable|sometimes|email|max:150',
            'author_realname' => 'nullable|sometimes|max:150',
            'status' => [
                'required',
                Rule::in(
                    PostStatus::all()
                )
            ],
            'post_content.*.form_id'                   => [
                'same:form_id'
            ],
            'post_content.*.fields'                   => [
                'present'
            ],
            'post_content.*.fields.*.required' => [
                function ($attribute, $value, $fail) {
                    if (!!$value) {
                        $field_content = request()->input(str_replace('.required', '', $attribute));
                        $label = $field_content['label'] ?: $field_content['id'];
                        $get_value = request()->input(str_replace('.required', '.value.value', $attribute));
                        $is_empty = (is_null($get_value) || $get_value === '');
                        $is_title = request()->input(str_replace('.required', '.type', $attribute)) === 'title';
                        $is_desc = request()->input(str_replace('.required', '.type', $attribute)) === 'description';
                        if ($is_empty && !$is_desc && !$is_title) {
                            return $fail(
                                trans('validation.required_by_label', [
                                    'label' => $label
                                ])
                            );
                        }
                    }
                }
            ],
            'post_content.*.fields.*.type' => [
                function ($attribute, $value, $fail) {
                    $get_value = request()->input(str_replace('.type', '.value.value', $attribute));
                    if ($value === 'tags' && !is_array($get_value)) {
                        return $fail(trans('validation.tag_field_must_be_array'));
                    }
                }
            ],
            'locale',
            'post_date'
        ];
    }//end getRules()

    /**
     * Get the post's translation.
     */
    public function translations()
    {
        return $this->morphMany('v5\Models\Translation', 'translatable');
    }//end translations()


    public function getUserIdAttribute($value)
    {
        return HideAuthor::hideAuthor($value, $this->survey ? $this->survey->hide_author : true, $value);
    }

    public function getAuthorEmailAttribute($value)
    {
        return HideAuthor::hideAuthor(
            $value,
            $this->survey ? $this->survey->hide_author : true,
            $this->getAttributeValue('user_id')
        );
    }

    public function getAuthorRealnameAttribute($value)
    {
        return HideAuthor::hideAuthor(
            $value,
            $this->survey ? $this->survey->hide_author : true,
            $this->getAttributeValue('user_id')
        );
    }
    /**
     * @return bool
     */
    public function getPostDateAttribute($value)
    {
        return HideTime::hideTime($value, $this->survey ? $this->survey->hide_time : true);
    }

    /**
     * @return bool
     */
    public function getUpdatedAttribute($value)
    {
        $time = HideTime::hideTime($value, $this->survey ? $this->survey->hide_time : true);
        return self::makeDate($time);
    }
    /**
     * @return bool
     */
    public function getCreatedAttribute($value)
    {
        $time = HideTime::hideTime($value, $this->survey ? $this->survey->hide_time : true);
        return self::makeDate($time);
    }

    public function setPostDateAttribute($value)
    {
        // Set default value for post_date
        if (empty($value)) {
            $value = date_create()->format("Y-m-d H:i:s");
            // Convert post_date to mysql format
        } else {
            $value = date_create($value)->format("Y-m-d H:i:s");
        }
        $this->attributes['post_date'] = $value;
    }

    /* -- Post status management methods -- */

    /**
     * Can the post be made public?
     *
     * Returns:
     *   - Null if already public
     *   - True if possible
     *   - Error message if not
     */
    protected function canBePublished()
    {
        if ($this->status === PostStatus::PUBLISHED) {
            return;
        }

        // Is the post in a publishable status?
        if (!PostStatus::isValidTransition($this->status, PostStatus::PUBLISHED)) {
            return trans('post.invalidStatusTransition');
        }

        // Are there stages/tasks that require completion AND are not completed?
        $pending_tasks = $this->survey->getMissingRequiredTasks($this->completed_stages);
        if (count($pending_tasks) > 0) {
            // TODO: translate the label as necessary
            return trans('post.stageRequired', ['param1' => $pending_tasks[0]->label]);
        }

        return true;
    }

    /**
     *
     */
    public function tryAutoPublish()
    {
        // Is automatic publishing enabled in the survey? ( require_approval: false )
        if ($this->survey->require_approval) {
            return false;
        }

        // Can it be published? Otherwise, give up
        if ($this->canBePublished() === false) {
            return false;
        }

        // Publish
        $this->setAttribute('status', PostStatus::PUBLISHED);
        return true;
    }

    /**
     * Perform user-requested status change
     * Note that this is different from just setting the status in the model,
     * this actually performs flow checks.
     */
    public function doStatusTransition($new_status)
    {
        if ($this->status === $new_status) {
            return;
        }

        // Is the post in a publishable status?
        if (!PostStatus::isValidTransition($this->status, $new_status)) {
            return trans('post.invalidStatusTransition');
        }

        if ($new_status === PostStatus::PUBLISHED) {
            $pending_tasks = $this->survey->getMissingRequiredTasks($this->completed_stages);
            if (count($pending_tasks) > 0) {
                // TODO: translate the label as necessary
                return trans('post.stageRequired', ['param1' => $pending_tasks[0]->label]);
            }
        }

        $this->setAttribute('status', $new_status);
    }

    /* -- Relations accessors -- */

    public function survey()
    {
        return $this->hasOne('v5\Models\Survey', 'id', 'form_id')->setEagerLoads([]);
    }

    public function locks()
    {
        return $this->hasMany('v5\Models\PostValues\PostLock', 'post_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany('v5\Models\Category', 'posts_tags', 'post_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany('v5\Models\Comment', 'post_id', 'id');
    }

    public function message()
    {
        return $this->hasOne(Message::class);
    }

    // public function contact()
    // {
    //     // Lumen 5.8+:
    //     // return $this->hasOneThrough(Message::class, Contact::class);
    // }

    protected static function valueTypesRelationships()
    {
        $value_types = [
            'Varchar',
            'Text',
            'Datetime',
            'Decimal',
            'Geometry',
            'Int',
            'Markdown',
            'Media',
            'Point',
            'Relation',
            'PostsMedia',
//            'PostsSet',
            'PostTag'
        ];
        return array_map(function ($t) {
            return "values${t}";
        }, $value_types);
    }

    /*
     * Convenience accessor to fetch posts along with their values
     */
    public static function withPostValues()
    {
        return Post::with(Post::valueTypesRelationships());
    }

    public function getPostValues()
    {
        $values = [];
        foreach ($this->valueTypesRelationships() as $rel) {
            if ($rel == 'valuesPostTag') {
                // For categories, preload the categories key relations
                $values[] = $this->valuesPostTag()->with(['tag.parent', 'tag.children', 'tag.translations'])->get();
            } else {
                $values[] = $this->{"$rel"};
            }
        }
        return Collection::make(Arr::flatten($values));
    }

    /**
     * Post values relationships
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function valuesVarchar()
    {
        return $this->hasMany('v5\Models\PostValues\PostVarchar', 'post_id', 'id')
            ->select('post_varchar.*');
    }

    public function valuesText()
    {
        return $this->hasMany('v5\Models\PostValues\PostText', 'post_id', 'id')
            ->select('post_text.*');
    }

    public function valuesDatetime()
    {
        return $this->hasMany('v5\Models\PostValues\PostDatetime', 'post_id', 'id')
            ->select('post_datetime.*');
    }

    public function valuesDecimal()
    {
        return $this->hasMany('v5\Models\PostValues\PostDecimal', 'post_id', 'id')
            ->select('post_decimal.*');
    }

    public function valuesGeometry()
    {
        return $this->hasMany('v5\Models\PostValues\PostGeometry', 'post_id', 'id');
    }

    public function valuesInt()
    {
        return $this->hasMany('v5\Models\PostValues\PostInt', 'post_id', 'id')
            ->select('post_int.*');
    }

    public function valuesMarkdown()
    {
        return $this->hasMany('v5\Models\PostValues\PostMarkdown', 'post_id', 'id')
            ->select('post_markdown.*');
    }

    public function valuesMedia()
    {
        return $this->hasMany('v5\Models\PostValues\PostMedia', 'post_id', 'id')
            ->select('post_media.*');
    }

    public function valuesPoint()
    {
        return $this->hasMany('v5\Models\PostValues\PostPoint', 'post_id', 'id');
        ;
    }

    public function valuesRelation()
    {
        return $this->hasMany('v5\Models\PostValues\PostRelation', 'post_id', 'id')
            ->select('post_relation.*');
    }

    public function valuesPostsMedia()
    {
        return $this->hasMany('v5\Models\PostValues\PostsMedia', 'post_id', 'id')
            ->select('posts_media.*');
    }

    public function valuesPostsSet()
    {
        return $this->hasMany('v5\Models\PostValues\PostsSet', 'post_id', 'id')
            ->select('posts_sets.*');
    }

    public function valuesPostTag()
    {
        return $this->hasMany('v5\Models\PostValues\PostTag', 'post_id', 'id')
            ->select('posts_tags.*');
    }

    public function postStages()
    {
        return $this->hasMany('v5\Models\PostStages', 'post_id', 'id');
    }
}//end class
