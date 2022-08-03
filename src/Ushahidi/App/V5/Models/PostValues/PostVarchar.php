<?php

namespace Ushahidi\App\V5\Models\PostValues;

class PostVarchar extends PostValue
{
    public $table = 'post_varchar';
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
            'value' => ['string', 'max:255'],
        ];
        return array_merge(parent::getRules(), $rules);
    }//end getRules()

    /**
     * @return bool
     */
    public function getValueAttribute($value)
    {
        if (isset($value) && $this->attribute->input === 'checkbox') {
            return $this->decodeCheckboxValue($value);
        }
        return $value;
    }

    protected function decodeCheckboxValue($value)
    {
        // Our current approach here is to store the checkbox array value
        // encoded as JSON
        if ($value[0] === '[' && ($v = json_decode($value)) != null) {
            return $v;
        }

        // However, that wasn't always the case and some datasets may have
        // this encodded as comma separated values. Note that this didn't
        // support having commas in the checkbox labels.
        return explode(",", $value);
    }

    public function setValueAttribute($value)
    {
        if (isset($value) && $this->attribute->input === 'checkbox') {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}//end class
