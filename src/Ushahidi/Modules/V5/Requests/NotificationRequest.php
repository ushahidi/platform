<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Notification;
use Illuminate\Http\Request;
use Ushahidi\Contracts\Notification as NotificationTypes;

class NotificationRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {
        $notification_id = $request->route('id') ? $request->route('id') : null;

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
            'user_id' => 'nullable|sometimes|exists:users,id',
            'set_id' => 'required|integer|exists:sets,id'
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
            'user_id' => 'filled|integer|exists:users,id',
            'set_id' => 'filled|integer|exists:sets,id'
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.user_id')]
            ),
            'user_id.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.user_id')]
            ),
            'user_id.integer' => trans(
                'validation.integer',
                ['field' => trans('fields.user_id')]
            ),

            'set_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.set_id')]
            ),
            'set_id.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.set_id')]
            ),
            'set_id.integer' => trans(
                'validation.integer',
                ['field' => trans('fields.set_id')]
            ),
        ];
    }
}
