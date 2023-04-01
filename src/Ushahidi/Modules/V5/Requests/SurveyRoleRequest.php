<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;

class SurveyRoleRequest extends BaseRequest
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
                'roles'=>['array'],
                'roles.*'=>['int','exists:roles,id']

            ];
        } elseif ($request->isMethod('put')) {
            return [
                'roles'=>['array'],
                'roles.*'=>['int','exists:roles,id']
            ];
        } else {
            return [];
        }
    }

    public function messages()
    {
        return [

            'roles.array' => trans('validation.array', ['field' => trans('fields.roles')]),
            'roles.*.int' => trans('validation.string', ['field' => trans('fields.roles.name')]),
            'roles.*.exists' => trans('validation.exists', ['field' => trans('fields.roles.name')]),
        ];
    }
}
