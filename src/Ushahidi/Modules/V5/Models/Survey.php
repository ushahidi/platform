<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Ushahidi\Contracts\Permission;
use Illuminate\Support\Facades\Request as RequestFacade;
use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;

class Survey extends BaseModel
{
    use InteractsWithFormPermissions;
    public static $relationships = ['tasks', 'translations', 'enabled_languages'];

    public static $approved_fields_for_select = [
        'id',
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
        'base_language',
        'targeted_survey',
        'created',
        'updated'
    ];
    public static $required_fields_for_select = [
        'id',
        'name',
        'base_language',
    ];

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
        'created',
        'updated'
    ];



    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = ['can_create'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'report',
        'require_approval' => true,
        'everyone_can_create' => true,
        'hide_author' => false,
        'hide_time' => false,
        'disabled' => false,
        'hide_location' => false,
        'targeted_survey' => false,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'everyone_can_create' => 'boolean',
        'hide_author' => 'boolean',
        'hide_time' => 'boolean',
        'hide_location' => 'boolean',
        'targeted_survey' => 'boolean',
        'require_approval' => 'boolean',
        'disabled' => 'boolean',
    ];


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


    // /**
    //  * We check for relationship permissions here, to avoid hydrating anything that should not be hydrated.
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    public function tasks()
    {
        $authorizer = service('authorizer.form');
        $user = $authorizer->getUser();
        // NOTE: this acl->hasPermission check is all `canUserEditForm` does, so we're doing that directly
        // to avoid an hydration issue with InteractsWithFormPermissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            // if this permission is set we can go ahead and hydrate all the stages
            return $this->hasMany('Ushahidi\Modules\V5\Models\Stage', 'form_id')->orderBy("priority", "asc");
        }

        return $this->hasMany(
            'Ushahidi\Modules\V5\Models\Stage',
            'form_id'
        )
            ->where('form_stages.show_when_published', '=', '1')
            ->where('form_stages.task_is_internal_only', '=', '0')
            ->orderBy("priority", "asc");
    } //end tasks()


    /**
     * Get the survey's translation.
     */
    public function translations()
    {
        return $this->morphMany('Ushahidi\Modules\V5\Models\Translation', 'translatable');
    } //end translations()

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
    /**
     * get the  required columns .
     *
     * @param  Request  $request
     * @return array
     */
    public static function selectModelFields(Request $request): array
    {
        return self::includeFields($request, (new Survey())->fillable, [
            'id',
            'base_language',
        ]);
    }

    public function getCreatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
    public function getUpdatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
} //end class
