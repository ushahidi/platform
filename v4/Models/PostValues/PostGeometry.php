<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PostGeometry extends PostValue
{
    public $table = 'post_geometry';
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
                function ($attribute, $value, $fail) {
                    if (!is_scalar($value)) {
                        return $fail(trans('validation.post_values.geometry'));
                    }
                }
            ]
        ];
        return [parent::getRules(), $rules];
    }//end getRules()
}//end class
