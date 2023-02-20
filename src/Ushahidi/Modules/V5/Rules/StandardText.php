<?php

namespace Ushahidi\Modules\V5\Rules;

use Illuminate\Contracts\Validation\Rule;

class StandardText implements Rule
{
    protected $patterns = [
        // Regex that only allows letters, numbers, punctuation, and space.
        '/^[\pL\pN\pP ]++$/uD',
        // Regex for bengali, gujarati, kannada, latin characters and numbers, punctuation and space
        '/^[\p{Bengali}\p{Gujarati}\p{Kannada}\p{Malayalam}\p{Telugu}\pL\pN\pP ]{0,100}$/u',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $passes = false; // Default pass value

        foreach ($this->patterns as $pattern) {
            $passes = preg_match($pattern, $value);
            if ($passes) { // if match is true
                break;
            }
        }

        return $passes;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans(
            'validation.regex',
            [
                'field' => trans('fields.title'),
            ]
        );
    }
}
