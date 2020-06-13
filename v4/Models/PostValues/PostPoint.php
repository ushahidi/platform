<?php

namespace v4\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostPoint extends PostValue
{
    public $table = 'post_point';
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
                'array',
                function ($attribute, $value, $fail) {
                    if (!(isset($value['lat'])) || !(isset($value['lon']))) {
                        return $fail(trans('validation.post_values.point.format'));
                    }
                    if (!($this->checkLat($value['lat']))) {
                        return $fail(trans('validation.post_values.point.lat'));
                    }

                    if (!($this->checkLon($value['lon']))) {
                        return $fail(trans('validation.post_values.point.lon'));
                    }
                }
            ],
        ];
        return [parent::getRules(), $rules];
    }//end getRules()

    private function checkLon($lon)
    {
        if (!is_numeric($lon)) {
            return false;
        }

        if ($lon < -180 || $lon > 180) {
            return false;
        }

        return true;
    }

    private function checkLat($lat)
    {
        if (!is_numeric($lat)) {
            return false;
        }

        if ($lat < -90 || $lat > 90) {
            return false;
        }

        return true;
    }
}//end class
