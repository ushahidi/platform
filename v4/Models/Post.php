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

class Post extends ResourceModel
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
        'base_language',
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
    ];


    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        $authorizer = service('authorizer.post');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();

        $postPermissions = new \Ushahidi\Core\Tool\Permissions\PostPermissions();
        $postPermissions->setAcl($authorizer->acl);
        $excludePrivateValues = !$postPermissions->canUserReadPrivateValues(
            $user
        );

        return $query;
    }

    // OhanzeeRepository
    public function getEntity(array $data = null): Post
    {
        // Ensure we are dealing with a structured Post

        $user = $this->getUser();
        $excludePrivateValues = true;
        $excludeStages = [];
        $values = $data['values'] ?? [];

        // Check post permissions
        // @todo move or double up in formatter. That should enforce what users can see
        $excludePrivateValues = !$this->postPermissions->canUserReadPrivateValues(
            $user
        );

        $this->post_value_factory->getRepo('point')->hideLocation(
            !$this->postPermissions->canUserSeeLocation(
                $user,
                new Post($data),
                $this->form_repo
            )
        );

        if (!empty($data['form_id'])) {
            // Get Hidden Stage Ids to be excluded from results
            $excludeStages = $this->form_stage_repo->getHiddenStageIds(
                $data['form_id'],
                $data['status']
            );
        }

        if (!empty($data['id'])) {
            // NOTE: This and the restriction above belong somewhere else,
            // ideally in their own step
            // Check if time info should be returned
            if (!$this->postPermissions->canUserSeeTime($user, new Post($data), $this->form_repo)) {
                // Hide time on survey fields
                $this->post_value_factory->getRepo('datetime')->hideTime(true);

                // @todo move to formatter. That where this normally happens
                // Replace time with 00:00:00
                if ($postDate = date_create($data['post_date'], new \DateTimeZone('UTC'))) {
                    $data['post_date'] = $postDate->setTime(0, 0, 0)->format('Y-m-d H:i:s');
                }
                if ($created = date_create('@'.$data['created'], new \DateTimeZone('UTC'))) {
                    $data['created'] = $created->setTime(0, 0, 0)->format('U');
                }
                if ($updated = date_create('@'.$data['updated'], new \DateTimeZone('UTC'))) {
                    $data['updated'] = $updated->setTime(0, 0, 0)->format('U');
                }
            }

            if (!$this->postPermissions->canUserSeeAuthor($user, new Post($data), $this->form_repo)
                && ($data['author_realname'] || $data['user_id'] || $data['author_email'])) {
                // @todo move to formatter. That where this normally happens
                unset($data['author_realname']);
                unset($data['author_email']);
                unset($data['user_id']);
            }

            /* -- VALUES HANDLING -- */

            /* handle values already carried in in the $data object */
            $already_obtained_types = [];
            foreach ($this->data_to_entity_value_mappings as $mapping) {
                // Check if value should be visible
                $attribute_key = $data[$mapping['attribute_key']] ?? null;
                $attribute_value = $data[$mapping['value']] ?? null;

                // Skip if data not provided
                if (!$attribute_key || !$attribute_value) {
                    continue;
                }

                // Check visibility
                $attribute = $this->form_attributes_by_key->get($attribute_key);
                // .. exclude values marked as private
                if ($excludePrivateValues && $attribute['response_private']) {
                    continue;
                }
                // .. exclude stages
                if ($excludeStages && in_array($attribute['form_stage_id'], $excludeStages)) {
                    continue;
                }
                // .. exclude non-mentioned attributes
                if ($this->include_attributes && !in_array($attribute_key, $this->include_attributes)) {
                    continue;
                }

                // Build and set values
                $value = $this->post_value_factory
                    ->getRepo($mapping['repo'])
                    ->getEntity(['value' => $attribute_value]);
                $values[$attribute_key] = [ $value->value ];

                $already_obtained_types = array_merge($already_obtained_types, [ $mapping['obtained_type'] ]);

                // Unset original values
                unset($data[$mapping['attribute_key']]);
                unset($data[$mapping['value']]);
            }

            // Obtain the rest of the requested values
            $other_values = [];
            $types_to_fetch = null;

            if ($this->form_attributes_by_form) {
                /* Check which types are used in the form */
                $form_attributes = $this->form_attributes_by_form->get(intval($data['form_id']));
                if ($form_attributes) {
                    /* Count how many of each form attribute type we have */
                    $types_to_fetch_with_attribute_count = $form_attributes
                        ->groupBy('type')
                        ->map(function ($attrs) {
                            return $attrs->count();
                        });

                    $types_to_fetch = $types_to_fetch_with_attribute_count->keys()->toArray();

                    /* Intersect with requested types */
                    if (count($this->include_value_types) > 0) {
                        $types_to_fetch = array_intersect($types_to_fetch, $this->include_value_types);
                    }

                    /* drop types that we have already fetched
                       BUT only if there is a SINGLE attribute of that type in the form,
                       since the SQL JOINs that we have run previously are only good for fetching
                       a SINGLE value of each type for each post.
                       If we don't do this, we would be leaving out the values of the second
                       and subsequent attributes defined with that type */
                    $already_obtained_types = collect($already_obtained_types)
                        ->filter(function ($type) use ($types_to_fetch_with_attribute_count) {
                            return $types_to_fetch_with_attribute_count->get($type) < 2;
                        })->toArray();
                    $types_to_fetch = array_diff($types_to_fetch, $already_obtained_types);
                }
            }

            if ($types_to_fetch === null || !empty($types_to_fetch)) {
                $other_values = $this->getPostValues(
                    $data['id'],
                    $excludePrivateValues,
                    $excludeStages,
                    $types_to_fetch ?? $this->include_value_types
                );
            }

            //
            $data['values'] = $other_values + $values;

            // If we are not limiting ourselves to the most basic core properites
            if ($this->search_output_type !== 'core') {
                // Continued for legacy
                $data['tags'] = $this->getTagsForPost($data['id'], $data['form_id']);
                $data['sets'] = $this->getSetsForPost($data['id']);
                $data['completed_stages'] = $this->getCompletedStagesForPost(
                    $data['id'],
                    $excludePrivateValues,
                    $excludeStages
                );
                $data['lock'] = null;

                // @todo move or double up in formatter. That should enforce what users can see
                if ($this->postPermissions->canUserSeePostLock($user, new Post($data))) {
                    $data['lock'] = $this->getHydratedLock($data['id']);
                }
            }
        }

        return new Post($data);
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

    public function getPostDateAttribute($value)
    {
        return $value;
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
            'PostsSet',
            'PostsTag'
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
        return $this->hasMany('v4\Models\PostValues\PostVarchar', 'post_id', 'id');
    }

    public function valuesText()
    {
        return $this->hasMany('v4\Models\PostValues\PostText', 'post_id', 'id');
    }

    public function valuesDatetime()
    {
        return $this->hasMany('v4\Models\PostValues\PostDatetime', 'post_id', 'id');
    }

    public function valuesDecimal()
    {
        return $this->hasMany('v4\Models\PostValues\PostDecimal', 'post_id', 'id');
    }

    public function valuesGeometry()
    {
        return $this->hasMany('v4\Models\PostValues\PostGeometry', 'post_id', 'id');
    }

    public function valuesInt()
    {
        return $this->hasMany('v4\Models\PostValues\PostInt', 'post_id', 'id');
    }

    public function valuesMarkdown()
    {
        return $this->hasMany('v4\Models\PostValues\PostMarkdown', 'post_id', 'id');
    }

    public function valuesMedia()
    {
        return $this->hasMany('v4\Models\PostValues\PostMedia', 'post_id', 'id');
    }

    public function valuesPoint()
    {
        return $this->hasMany('v4\Models\PostValues\PostPoint', 'post_id', 'id');
    }

    public function valuesRelation()
    {
        return $this->hasMany('v4\Models\PostValues\PostRelation', 'post_id', 'id');
    }

    public function valuesPostsMedia()
    {
        return $this->hasMany('v4\Models\PostValues\PostsMedia', 'post_id', 'id');
    }

    public function valuesPostsSet()
    {
        return $this->hasMany('v4\Models\PostValues\PostsSet', 'post_id', 'id');
    }

    public function valuesPostsTag()
    {
        return $this->hasMany('v4\Models\PostValues\PostsTag', 'post_id', 'id');
    }
}//end class
