<?php

namespace v5\Http\Controllers;

use Illuminate\Support\Facades\Validator;

class ValidationRunner
{

    public static function runValidation($data, $rules, $messages)
    {
        $v = Validator::make($data, $rules, $messages);
        // check for failure
        if (!$v->fails()) {
            return [ "ok" => true, "errors" => null ];
        }
        // set errors and return false
        return [ "ok" => false, "errors" => $v->errors() ];
    }
}
