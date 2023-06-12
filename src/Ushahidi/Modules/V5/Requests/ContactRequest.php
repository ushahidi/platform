<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Contact;
use Illuminate\Http\Request;
use Ushahidi\Contracts\Contact as ContactTypes;

class ContactRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {
        $contact_id = $request->route('id') ? $request->route('id') : null;

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
            'type' => [
                'required',
                'min:2',
                'max:255',
                Rule::in([
                    ContactTypes::EMAIL,
                    ContactTypes::PHONE,
                    ContactTypes::TWITTER,
                    ContactTypes::WHATSAPP
                ])
            ],
            'contact' => [
                'required',
                'min:3',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'phone' && (strlen(preg_replace('/\D+/', '', $value))===0)) {
                        $fail($attribute . ' must be a valid phone number.');
                    } elseif ($request->type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail($attribute . ' must be a valid email address.');
                    }
                },
            ],
            'data_source' => [
                'nullable',
                Rule::in($sources->getEnabledSources())
            ]
        ];
    }

    private function updateRules(Request $request): array
    {
        $sources = app('datasources');
        return [
            'user_id' => 'nullable|sometimes|exists:users,id',
            'type' => [
                'filled',
                'min:2',
                'max:255',
                Rule::in([
                    ContactTypes::EMAIL,
                    ContactTypes::PHONE,
                    ContactTypes::TWITTER,
                    ContactTypes::WHATSAPP
                ])
            ],
            'contact' => [
                'filled',
                'min:3',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'phone' && (strlen(preg_replace('/\D+/', '', $value))===0)) {
                        $fail($attribute . ' must be a valid phone number.');
                    } elseif ($request->type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail($attribute . ' must be a valid email address.');
                    }
                },
            ],
            'data_source' => [
                'nullable',
                Rule::in($sources->getEnabledSources())
            ]
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.user_id')]
            ),
            'type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.type')]
            ),
            'type.unique' => trans(
                'validation.unique',
                ['field' => trans('fields.type')]
            ),
            'type.min' => trans(
                'validation.min_length',
                [
                    'param2' => 3,
                    'field' => trans('fields.type'),
                ]
            ),
            'type.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.type'),
                ]
            ),
            'type.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.type'),
                ]
            ),
            'contact.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.contact')]
            ),
            'contact.min' => trans(
                'validation.min_length',
                [
                    'param2' => 3,
                    'field' => trans('fields.contact'),
                ]
            ),
            'contact.max' => trans(
                'validation.max_length',
                [
                    'param2' => 3,
                    'field' => trans('fields.contact'),
                ]
            ),
            'contact.regex' => trans(
                'validation.regex',
                ['field' => trans('fields.contact')]
            ),

            'contact.email' => trans(
                'validation.email',
                ['field' => trans('fields.contact')]
            ),

            'data_source.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.data_source')]
            ),
        ];
    }
}
