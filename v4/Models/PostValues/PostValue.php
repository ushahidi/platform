<?php

namespace v4\Models\PostValues;

use v4\Models\Scopes\PostValueAllowed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PostValue extends Model
{
    /**
     * Add eloquent style timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created',
    ];

    /**
     * @var array
    */
    protected $fillable = [
        'post_id',
        'form_attribute_id',
        'value',
    ];

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
//    public function scopeAllowed($query)
//    {
//        /**
//         * If no roles are selected, the Tag is considered
//         * completely public.
//         */
//        $authorizer = service('authorizer.post');
//        $user = $authorizer->getUser();
//
//        $postPermissions = new \Ushahidi\Core\Tool\Permissions\PostPermissions();
//        $postPermissions->setAcl($authorizer->acl);
//        $excludePrivateValues = !$postPermissions->canUserReadPrivateValues(
//            $user
//        );
//
//        $q = $query
//            ->join('form_attributes', $this->table.'.form_attribute_id', '=', 'form_attributes.id');
//
//        if ($excludePrivateValues) {
//            $q = $q->where('form_attributes.response_private', '=', 0);
//        }
//
//        return $q;
//    }
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PostValueAllowed);
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
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'form_attribute_id' => 'nullable|sometimes|exists:form_attributes,id',
        ];
    }//end getRules()

    public function validate($data)
    {
        $v = Validator::make($data, $this->getRules(), self::validationMessages());
        // check for failure
        if (!$v->fails()) {
            return true;
        }
        // set errors and return false
        $this->errors = $v->errors();
        return false;
    }


    // ValuesForPostRepository
    public function getAllForPost(
        $post_id,
        array $include_attributes = [],
        array $exclude_stages = [],
        $excludePrivateValues = true
    ) {
        $query = $this->selectQuery(compact('post_id'));

        if ($include_attributes) {
            $query->where('form_attributes.key', 'IN', $include_attributes);
        }

        if ($excludePrivateValues) {
            $query->where('form_attributes.response_private', '!=', '1');
            if ($exclude_stages) {
                $query->where('form_attributes.form_stage_id', 'NOT IN', $exclude_stages);
            }
        }

        $results = $query->execute($this->db());

        return $this->getCollection($results->as_array());
    }

    public function attribute()
    {
        return $this->hasOne('v4\Models\Attribute', 'id', 'form_attribute_id');
    }

    public function post()
    {
        return $this->hasOne('v4\Models\Post', 'id', 'post_id');
    }
}//end class
