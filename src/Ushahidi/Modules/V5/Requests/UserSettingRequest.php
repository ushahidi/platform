<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;

class UserSettingRequest extends BaseRequest
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
                'config_key' => [
                    'required', 'unique:user_settings,config_key,NULL,id,user_id,' . $request->route('user_id'),
                    'string',
                    'min:3',
                    'max:255'
                ],
                'config_value' => ['required'],
            ];
        } elseif ($request->isMethod('put')) {
            return [
                'config_key' => [
                    'filled',
                    'unique:user_settings,config_key,' . $request->route('id')
                    . ',id,user_id,' . $request->route('user_id'),
                    'string',
                    'min:3',
                    'max:255'
                ],
                'config_value' => ['filled', 'min:3', 'max:255'],
            ];
        } else {
            return [];
        }
    }

    public function messages()
    {
        return [
            'config_key.required' => trans('validation.not_empty', ['field' => trans('fields.config_key')]),
            'config_key.unique' => trans('validation.unique', ['field' => trans('fields.config_key')]),
            'config_key.max' => trans('validation.max', ['field' => trans('fields.config_key')]),
            'config_key.min' => trans('validation.min', ['field' => trans('fields.config_key')]),
            'config_key.string' => trans('validation.string', ['field' => trans('fields.config_key')]),
            'config_key.filled' => trans('validation.not_empty', ['field' => trans('fields.config_key')]),

            'config_value.required' => trans('validation.not_empty', ['field' => trans('fields.config_value')]),
            'config_value.max' => trans('validation.max', ['field' => trans('fields.config_value')]),
            'config_value.min' => trans('validation.min', ['field' => trans('fields.config_value')]),
            'config_value.string' => trans('validation.string', ['field' => trans('fields.config_value')]),
            'config_value.filled' => trans('validation.not_empty', ['field' => trans('fields.config_value')]),
 
        ];
    }
}
