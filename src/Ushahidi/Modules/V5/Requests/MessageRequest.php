<?php

namespace Ushahidi\Modules\V5\Requests;

use Google\Service\CivicInfo\MessageSet;
use Google\Service\CloudSearch\MessageDeleted;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Message;
use Illuminate\Http\Request;
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Contracts\MessageDirection;

class MessageRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {
        $message_id = $request->route('id') ? $request->route('id') : null;

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
            'parent_id' => 'nullable|sometimes|exists:messages,id',
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'contact_id' => 'nullable|sometimes|exists:contacts,id',
            'direction' => ['required', Rule::in([MessageDirection::OUTGOING])],
            'message' => ['required'],
            'type' => ['nullable', Rule::in(['sms', 'ivr', 'email', 'twitter'])],
            'status' => ['nullable', Rule::in([MessageStatus::PENDING, MessageStatus::RECEIVED, MessageStatus::SENT])],
            'data_source_message_id' => ['max:511'],
            'data_source' => [
                'nullable',
                'max:150',
                Rule::in($sources->getEnabledSources())
            ]
        ];
    }

    private function updateRules(Request $request): array
    {
        $sources = app('datasources');
        return [
            'user_id' => 'nullable|sometimes|exists:users,id',
            'parent_id' => 'nullable|sometimes|exists:messages,id',
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'contact_id' => 'nullable|sometimes|exists:contacts,id',
            'direction' => ['filled', Rule::in([MessageDirection::OUTGOING, MessageDirection::INCOMING])],
            'message' => ['filled'],
            'type' => ['filled', Rule::in(['sms', 'ivr', 'email', 'twitter'])],
            'status' => ['filled', Rule::in([
                MessageStatus::PENDING,
                MessageStatus::RECEIVED,
                MessageStatus::SENT,
                MessageStatus::EXPIRED,
                MessageStatus::CANCELLED,
                MessageStatus::ARCHIVED
            ])],
            'data_source_message_id' => ['max:511'],
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
            'parent_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.parent_id')]
            ),
            'post_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.post_id')]
            ),
            'contact_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.contact_id')]
            ),


            'type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.type')]
            ),
            'type.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.type')]
            ),

            'direction.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.direction')]
            ),
            'direction.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.direction')]
            ),


            'status.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.status')]
            ),
            'status.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.status')]
            ),


            'message.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.message')]
            ),
            'data_source.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.data_source')]
            ),

            'data_source_message_id.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.data_source_message_id'),
                ]
            ),
            'data_source.max' => trans(
                'validation.max_length',
                [
                    'param2' => 150,
                    'field' => trans('fields.data_source'),
                ]
            ),
        ];
    }
}
