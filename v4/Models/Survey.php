<?php

namespace v4\Models;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;

class Survey extends Model
{
    use InteractsWithFormPermissions;
    protected $table = 'forms';
    protected $with = ['stages'];
    public $timestamps = FALSE;
    /**
     * The attributes that should be hidden for serialization.
     * @note this should be changed so that we either use the fractal transformer
     * OR a policy authorizer which is a more or less accepted method to do it
     * (which uses the same $hidden type thing but it's much nicer obviously)
     *
     * @var array
     */
    protected $hidden = ['description'];
    /**
     * The attributes that should be mutated to dates.
     * @var array
    */
    protected $dates = ['created', 'updated'];

    /**
    * The attributes that are mass assignable.
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
        'targeted_survey'
    ];

    /**
     * We check for relationship permissions here, to avoid hydrating anything that should not be hydrated.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stages()
    {
        $authorizer = service('authorizer.form');
        $user = $authorizer->getUser();
        // NOTE: this acl->hasPermission check is all `canUserEditForm` does, so we're doing that directly
        // to avoid an hydration issue with InteractsWithFormPermissions
        if ($authorizer->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            // if this permission is set we can go ahead and hydrate all the stages
            return $this->hasMany('v4\Models\Stage', 'form_id');
        }
        return $this->hasMany('v4\Models\Stage', 'form_id')
            ->where('form_stages.show_when_published', '=', '1')
            ->where('form_stages.task_is_internal_only', '=', '0');
    }

}
