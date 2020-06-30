<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostRelation extends PostValue
{
    public $table = 'post_relation';

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
