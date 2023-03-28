<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CollectionRequest extends BaseRequest
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
    public function rules(Request $request)
    {
        if ($request->isMethod('post')) {
            return [
                'name' => [
                    'required',
                    'unique:sets,name'
                ],
                'role' => [
                    'nullable',
                    'array'
                ],
                'view_options' => [
                    'nullable',
                    'array'
                ]
            ];
        } elseif ($request->isMethod('put')) {
            return [
                'name' => [
                    'filled',
                    'unique:sets,name,' . $request->route('id')
                ],
                'role' => [
                    'nullable',
                    'array'
                ],
                'view_options' => [
                    'nullable',
                    'array'
                ]
            ];
        } else {
            return [];
        }
    }

    public function messages()
    {
        return [
            'name.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
            'name.unique' => trans(
                'validation.unique',
                ['field' => trans('fields.name')]
            ),
            'name.filled' => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
            'role.array' => trans(
                'validation.array',
                ['field' => trans('fields.name')]
            ),
            'view_options.array' => trans(
                'validation.array',
                ['field' => trans('fields.name')]
            )
        ];
    }
}
