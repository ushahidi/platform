<?php

namespace Ushahidi\Modules\V5\Models\Concerns;

use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator;

trait HasValidator
{
    protected $validationRules = [];

    public function getRules()
    {
        return [];
    }

    public function validationMessages()
    {
        return [];
    }

    public function validate($data = [])
    {
        $input = array_merge($this->attributes, $data);
        $v = Validator::make($input, $this->getRules($data), $this->validationMessages());
        // check for failure
        if (!$v->fails()) {
            return true;
        }
        // set errors and return false
        $this->errors = $v->errors();
        return false;
    }

    /**
     * Attempt to validate input, if successful fill this object
     *
     * @param array $inputArray associative array of values for this object to validate against and fill this object
     */
    public function validateAndFill($inputArray)
    {
        // must validate input before injecting into model
        $this->validate($inputArray);
        $this->fill($inputArray);
    }
}
