<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreTosRequest extends FormRequest
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
            'tos_version_date'        => [
                'required',
                'date'
            ]
        ];
    }

    public function messages()
    {
        return [
            'tos_version_date.required'      => trans(
                'validation.not_empty',
                ['field' => trans('fields.tos_version_date')]
            ),
            'tos_version_date.date'      => trans(
                'validation.date',
                ['field' => trans('fields.tos_version_date')]
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
            $errors = $e->errors();

            throw new HttpResponseException(
                response()->json([
                    'error' => 422,
                    'messages' => $errors,
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\UnauthorizedException
     */
    protected function failedAuthorization()
    {
        parent::failedAuthorization();
    }
}
