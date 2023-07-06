<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Core\Entity\Permission;
use Illuminate\Support\Facades\Input;

class Set extends BaseModel
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
    protected $table = 'sets';

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
        'user_id',
        'name',
        'description',
        'filter',
        'view',
        'view_options',
        'role',
        'search',
        'featured',
        'created',
        'updated'
    ];

    /**
     * Return all validation rules
     *
     * @return array
     */
    public function getRules()
    {
        $authorizer = service('authorizer.tag');
        $user = $authorizer->getUser();

        return [
            'user_id' => [
                'nullable|sometimes|exists:users,id',
                function ($attribute, $value, $fail) use ($user) {
                    if (!($user && $value == $user->id)) {
                        return $fail(trans('validation.user_not_owner'));
                    }
                }
            ],
            'name' => [
                'not_empty',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD',
            ],
            'view' => [
                Rule::in(['map', 'list', 'chart', 'timeline', 'data'])
            ],
            'role' => [
                'nullable|sometimes|exists:users,id',
            ]
        ];
    } //end getRules()

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        return $query;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function getCreatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }
    public function getUpdatedAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : null;
    }



    public function setFilterAttribute($value)
    {
        $this->attributes['filter'] = $value ? json_encode($value) : null;
    }

    public function getFilterAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    public function setViewOptionsAttribute($value)
    {
        $this->attributes['view_options'] = $value ? json_encode($value) : null;
    }

    public function getViewOptionsAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = $value ? json_encode($value) : null;
    }

    public function getRoleAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    public function getFeaturedAttribute($value)
    {
        return (bool)$value;
    }
} //end class
