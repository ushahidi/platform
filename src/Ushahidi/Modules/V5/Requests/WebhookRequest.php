<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class WebhookRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {
        $webhook_id = $request->route('id') ? $request->route('id') : null;

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
        $sources = app('datasources');
        return [
            'user_id' => 'nullable|sometimes|exists:users,id',
            'name' => 'required|max:255',
            'shared_secret' => 'required|min:20',
            'url' => 'required|url',
            'event_type' => ['required', Rule::in(['create', 'delete', 'update'])],
            'entity_type' => ['required', Rule::in(['post'])],
        ];
    }
    
    private function updateRules(Request $request): array
    {
        $sources = app('datasources');
        return [
            'user_id' => 'nullable|sometimes|exists:users,id',
            'name' => 'filled|max:255',
            'shared_secret' => 'filled|min:20',
            'url' => 'filled|url',
            'event_type' => ['filled', Rule::in(['create', 'delete', 'update'])],
            'entity_type' => ['filled', Rule::in(['post'])],
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.user_id')]
            ),

            'name.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
            'name.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.name'),
                ]
            ),

            'shared_secret.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.shared_secret')]
            ),
            'shared_secret.min' => trans(
                'validation.min_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.shared_secret'),
                ]
            ),

            'url.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.url')]
            ),
            'url.url' => trans(
                'validation.url',
                [
                    'param2' => 255,
                    'field' => trans('fields.url'),
                ]
            ),


            'event_type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.event_type')]
            ),
            'event_type.in' => trans(
                'validation.in_array',
                [
                    'param2' => 255,
                    'field' => trans('fields.event_type'),
                ]
            ),

            'entity_type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.entity_type')]
            ),
            'entity_type.in' => trans(
                'validation.in_array',
                [
                    'param2' => 255,
                    'field' => trans('fields.entity_type'),
                ]
            ),


        ];
    }
}
