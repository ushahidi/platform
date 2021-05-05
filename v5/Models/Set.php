<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Core\Entity\Permission;

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
    protected $dates = [
        'created',
        'updated'
    ];
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
        'featured'
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
             'name'  => [
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
    }//end getRules()

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
}//end class
