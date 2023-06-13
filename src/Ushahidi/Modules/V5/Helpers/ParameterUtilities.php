<?php

namespace Ushahidi\Modules\V5\Helpers;

use Illuminate\Support\Facades\Auth;

class ParameterUtilities
{
    public static function getParameterAsArray($parameter_value)
    {
        $filter_values = [];
        if ($parameter_value) {
            if (is_array($parameter_value)) {
                $filter_values = $parameter_value;
            } else {
                $filter_values = explode(',', $parameter_value);
            }
        }
        return $filter_values;
    }

    public static function checkIfNullOrEmpty($value, $accept_empty_string = false)
    {
        if ($value === null) {
            return true;
        } elseif (trim($value) === "" && !$accept_empty_string) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkIfEmpty($value, $default = null)
    {
        if (trim($value) === "") {
            return $default;
        } else {
            return $value;
        }
    }

    public static function checkIfUserAdmin()
    {
        return (Auth::user()->role === "admin");
    }
}
