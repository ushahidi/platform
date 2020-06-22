<?php

namespace v4\Models;

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
use v4\Models\Helpers\HideAuthor;
use v4\Models\Helpers\HideTime;
use v4\Models\Scopes\PostAllowed;

class Post extends ResourceModel
{
    use InteractsWithPostPermissions;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    protected $dateFormat = 'U';
    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

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
    protected $with = ['survey', 'categories'];
    protected $translations;
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
        'updated',
        'post_date'
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
        'base_language'
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
        'status'              => 'draft'
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


    protected static function boot()
    {
        parent::boot();
        /**
         * This is cool because we don't have to worry about calling ::allowed
         * each time to be safe that we are only getting authorized data. It's saving us
         * from ourselves :)
         */
        static::addGlobalScope(new PostAllowed);
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function validationMessages()
    {
        return [
        ];
    }//end validationMessages()

    /**
     * Return all validation rules
     *
     * @return array
     */
    protected function getRules()
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
            'author_email' => ['email', 'max:150'],
            'author_realname' => ['string', 'max:150'],
            'status' => [
                'required',
                Rule::in(
                    [
                        'draft',
                        'archived',
                        'published'
                    ]
                )
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
        return $this->morphMany('v4\Models\Translation', 'translatable');
    }//end translations()


    public function getUserIdAttribute($value)
    {
        return HideAuthor::hideAuthor($value, $this->survey ? $this->survey->hide_author : true);
    }

    public function getAuthorEmailAttribute($value)
    {
        return HideAuthor::hideAuthor($value, $this->survey ? $this->survey->hide_author : true);
    }

    public function getAuthorRealnameAttribute($value)
    {
        return HideAuthor::hideAuthor($value, $this->survey ? $this->survey->hide_author : true);
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

    public function validate($data)
    {
        $input = array_merge($this->attributes, $data);
        $v = Validator::make($input, $this->getRules(), self::validationMessages());
        // check for failure
        if (!$v->fails()) {
            return true;
        }
        // set errors and return false
        $this->errors = $v->errors();
        return false;
    }

    public function survey()
    {
        return $this->hasOne('v4\Models\Survey', 'id', 'form_id');
    }

    public function locks()
    {
        return $this->hasMany('v4\Models\PostValues\PostLock', 'post_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany('v4\Models\Category', 'posts_tags', 'post_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany('v4\Models\Comment', 'post_id', 'id');
    }

    public function values()
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
        return $this->hasMany('v4\Models\PostValues\PostVarchar', 'post_id', 'id')
            ->select('post_varchar.*');
    }

    public function valuesText()
    {
        return $this->hasMany('v4\Models\PostValues\PostText', 'post_id', 'id')
            ->select('post_text.*');
    }

    public function valuesDatetime()
    {
        return $this->hasMany('v4\Models\PostValues\PostDatetime', 'post_id', 'id')
            ->select('post_datetime.*');
    }

    public function valuesDecimal()
    {
        return $this->hasMany('v4\Models\PostValues\PostDecimal', 'post_id', 'id')
            ->select('post_decimal.*');
    }

    public function valuesGeometry()
    {
        return $this->hasMany('v4\Models\PostValues\PostGeometry', 'post_id', 'id');
    }

    public function valuesInt()
    {
        return $this->hasMany('v4\Models\PostValues\PostInt', 'post_id', 'id')
            ->select('post_int.*');
    }

    public function valuesMarkdown()
    {
        return $this->hasMany('v4\Models\PostValues\PostMarkdown', 'post_id', 'id')
            ->select('post_markdown.*');
    }

    public function valuesMedia()
    {
        return $this->hasMany('v4\Models\PostValues\PostMedia', 'post_id', 'id')
            ->select('post_media.*');
    }

    public function valuesPoint()
    {
        return $this->hasMany('v4\Models\PostValues\PostPoint', 'post_id', 'id');
        ;
    }

    public function valuesRelation()
    {
        return $this->hasMany('v4\Models\PostValues\PostRelation', 'post_id', 'id')
            ->select('post_relation.*');
    }

    public function valuesPostsMedia()
    {
        return $this->hasMany('v4\Models\PostValues\PostsMedia', 'post_id', 'id')
            ->select('posts_media.*');
    }

    public function valuesPostsSet()
    {
        return $this->hasMany('v4\Models\PostValues\PostsSet', 'post_id', 'id')
            ->select('posts_sets.*');
    }

    public function valuesPostTag()
    {
        return $this->hasMany('v4\Models\PostValues\PostTag', 'post_id', 'id')
            ->select('posts_tags.*');
    }

    public function postStages()
    {
        return $this->hasMany('v4\Models\PostStages', 'post_id', 'id');
    }
}//end class
