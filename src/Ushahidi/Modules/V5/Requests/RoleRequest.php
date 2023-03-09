<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;

class RoleRequest extends BaseRequest
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
                'name' => ['required','unique:roles,name','max:50'],
                'display_name' => ['required','max:50'],
                'permissions'=>['array'],
                'permissions.*'=>['string','exists:permissions,name']
            ];
        } elseif ($request->isMethod('put')) {
            return [
                'name' => ['filled','unique:roles,name','max:50'],
                'display_name' => ['filled','max:50'],
                'permissions'=>['array'],
                'permissions.*'=>['string','exists:permissions,name']
            ];
        } else {
            return [];
        }
    }

    public function messages()
    {
        return [
            'name.required' => trans('validation.not_empty', ['field' => trans('fields.name')]),
            'name.unique' => trans('validation.unique', ['field' => trans('fields.name')]),
            'name.max' => trans('validation.max', ['field' => trans('fields.name')]),
            'name.filled' => trans('validation.not_empty', ['field' => trans('fields.name')]),

            'display_name.required' => trans('validation.not_empty', ['field' => trans('fields.display_name')]),
            'display_name.max' => trans('validation.max', ['field' => trans('fields.display_name')]),
            'display_name.filled' => trans('validation.not_empty', ['field' => trans('fields.display_name')]),
          
            'permissions.array' => trans('validation.array', ['field' => trans('fields.permissions')]),
            'permissions.*.string' => trans('validation.string', ['field' => trans('fields.permissions.name')]),
            'permissions.*.exists' => trans('validation.exists', ['field' => trans('fields.permissions.name')]),
        ];
    }
}
