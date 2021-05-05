<?php

namespace v5\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ushahidi\App\Repository\FormRepository;
use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;
use Ushahidi\Core\Tool\Permissions\InteractsWithPostPermissions;

class PostStages extends BaseModel
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
    protected $table = 'form_stages_posts';

    /**
     * @var array
    */
    protected $fillable = [
        'post_id',
        'form_stage_id',
        'completed',
    ];

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        return $query;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [
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
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'form_stage_id' => 'nullable|sometimes|exists:form_stages,id',
            'completed' => Rule::in(0, 1)
        ];
    }//end getRules()
}//end class
