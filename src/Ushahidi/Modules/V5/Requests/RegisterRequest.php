<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;

class RegisterRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {

        if ($request->isMethod('post')) {
            return $this->storeRules($request);
        } elseif ($request->isMethod('put')) {
            return $this->updateRules($request);
        } else {
            return [];
        }
    }

    private function storeRules(Request $request): array
    {
        return [
            "email"=>['required','email','unique:users,email','max:150'],
            "password"=>['required','min:7','max:72']
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
        ];
    }


    public function messages(): array
    {
        return [
            'email.required' => trans('validation.not_empty', ['field' => trans('fields.email')]),
            'email.unique' => trans('validation.unique', ['field' => trans('fields.email')]),
            'email.max' => trans('validation.max', ['field' => trans('fields.email')]),

            'password.required' => trans('validation.not_empty', ['field' => trans('fields.password')]),
            'password.min' => trans('validation.min', ['field' => trans('fields.password')]),
            'password.max' => trans('validation.max', ['field' => trans('fields.password')]),


        ];
    }
}
