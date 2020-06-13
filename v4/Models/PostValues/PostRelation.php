<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostRelation extends PostValue
{
    public $table = 'post_relation';
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
        $rules = [
            'value' => [
                'numeric',
                Rule::exists('posts', 'id'),

            ],
        ];
        return [parent::getRules(), $rules];
    }//end getRules()
    // there is a validator in Relation (kohana verfsions) that I don't think ever happens?
    //    $post = $this->repo->get($value);
    //    if (is_int($this->config['input']['form']) && $post->form_id !== $this->config['input']['form']) {
    //        return 'invalidForm';
    //    }
}//end class
