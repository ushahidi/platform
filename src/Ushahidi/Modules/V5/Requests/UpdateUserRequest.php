<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateUserRequest extends FormRequest
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
            'email' => [
                'required',
                'unique'
            ],
            'password' => [
                'required'
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required'      => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
            'email.unique'      => trans(
                'validation.unique',
                ['field' => trans('fields.name')]
            ),
            'password.required'      => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        try {
            parent::failedValidation($validator);
        } catch (ValidationException $e) {
            throw new HttpResponseException(
                response()->json([
                    'error' => 422,
                    'messages' => $e->errors(),
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
