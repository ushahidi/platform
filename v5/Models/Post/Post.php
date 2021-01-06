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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ushahidi\App\Repository\FormRepository;
use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;
use Ushahidi\Core\Tool\Permissions\InteractsWithPostPermissions;
use v5\Models\Helpers\HideAuthor;
use v5\Models\Helpers\HideTime;
use v5\Models\Scopes\PostAllowed;

class Post extends BaseModel
{
    use InteractsWithPostPermissions;

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
    protected $with = ['translations'];
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

    public function getBulkPatchRules()
    {
        // our rules
        return [
            'bulk' => 'array',
            'bulk.*.status' => 'required, string',
        ];
    }

    public function bulkPatchValidation($data)
    {
        $v = Validator::make($data, $this->getBulkPatchRules(), $this->validationMessages());
        // check for failure
        if (!$v->fails()) {
            return true;
        }
        // set errors and return false
        $this->errors = $v->errors();
        return false;
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
                        $get_value = Input::get(str_replace('.required', '.value.value', $attribute));
                        $is_title = Input::get(str_replace('.required', '.type', $attribute)) === 'title';
                        $is_desc = Input::get(str_replace('.required', '.type', $attribute)) === 'description';
                        if (!$get_value && !$is_desc && !$is_title) {
                            return $fail(trans('validation.field_required'));
                        }
                    }
                }
            ],
            'post_content.*.fields.*.type' => [
                function ($attribute, $value, $fail) {
                    $get_value = Input::get(str_replace('.type', '.value.value', $attribute));
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

    public function survey()
    {
        return $this->hasOne('v5\Models\Survey', 'id', 'form_id');
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

    public function getPostValues()
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
        $values = [];
        foreach ($value_types as $type) {
            $values[] = $this->{"values$type"};
        }
        return Collection::make(array_flatten($values));
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
