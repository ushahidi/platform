<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;

class UserRequest extends BaseRequest
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
                'email' => ['required','unique:users,email','email','max:150'],
                'password' => ['required','min:7','max:72'],
                'realname'=>['max:150'],
                'role'=>['string','exists:roles,name']
            ];
        } elseif ($request->isMethod('put')) {
            return [
                'email' => ['filled','unique:users,email','email','max:150'],
                'password' => ['filled','min:7','max:72'],
                'realname'=>['max:150'],
                'role'=>['string','exists:roles,name']
            ];
        } else {
            return [];
        }
    }

    public function messages()
    {
        return [
            'email.required' => trans('validation.not_empty', ['field' => trans('fields.email')]),
            'email.unique' => trans('validation.unique', ['field' => trans('fields.email')]),
            'email.max' => trans('validation.max', ['field' => trans('fields.email')]),
            'email.email' => trans('validation.email', ['field' => trans('fields.email')]),
            'email.filled' => trans('validation.not_empty', ['field' => trans('fields.email')]),

            'password.required' => trans('validation.not_empty', ['field' => trans('fields.password')]),
            'password.max' => trans('validation.max', ['field' => trans('fields.password')]),
            'password.filled' => trans('validation.not_empty', ['field' => trans('fields.password')]),
          
            'realname.max' => trans('validation.max', ['field' => trans('fields.realname')]),

            'role.string' => trans('validation.string', ['field' => trans('fields.role')]),
            'role.exists' => trans('validation.exists', ['field' => trans('fields.role')]),
        ];
    }
}
