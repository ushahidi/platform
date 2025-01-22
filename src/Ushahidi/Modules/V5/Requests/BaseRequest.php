<?php
namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BaseRequest extends FormRequest
{
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
            $errors = [];
            foreach ($e->errors() as $field => $error_messages) {
                $errors[] = [
                    "field" => $field,
                    "error_messages" => $error_messages
                ];
            }
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        'status' => 422,
                        'message' => 'please recheck the your inputs',
                        'failed_validations' => $errors,
                    ]
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
