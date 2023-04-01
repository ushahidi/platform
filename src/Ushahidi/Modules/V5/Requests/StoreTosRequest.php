<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreTosRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tos_version_date' => [
                'required',
                'date'
            ]
        ];
    }

    public function messages()
    {
        return [
            'tos_version_date.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.tos_version_date')]
            ),
            'tos_version_date.date' => trans(
                'validation.date',
                ['field' => trans('fields.tos_version_date')]
            )
        ];
    }
}
