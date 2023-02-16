<?php

namespace Ushahidi\Modules\V5\Models;

class SurveyRole extends BaseModel
{
    /**
     * Specify the table
     *
     * @var string
     */
    protected $table = 'form_roles';

    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;



    /**
     * @var array
     */
    protected $fillable = [
        'form_id',
        'role_id'
    ];

    public function survey()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Survey', 'id', 'form_id');
    }

    public function role()
    {
        return $this->hasOne('Ushahidi\Modules\V5\Models\Role', 'id', 'role_id');
    }
} //end class
