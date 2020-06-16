<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PostVarchar extends PostValue
{
    public $table = 'post_varchar';
    public $with = ['translations'];

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
            'value' => ['string', 'max:255'],
        ];
        return [parent::getRules(), $rules];
    }//end getRules()


    /**
     * Get the post's translation.
     */
    public function translations()
    {
        return $this->morphMany('v4\Models\Translation', 'translatable');
    }//end translations()
}//end class
