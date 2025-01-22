<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Notification;
use Illuminate\Http\Request;
use Ushahidi\Core\Facade\Feature;

class MediaRequest extends BaseRequest
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
            'user_id' => 'nullable|sometimes|exists:users,id',
            'file' => 'required|file',
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
            'user_id' => 'nullable|sometimes|exists:users,id',
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.user_id')]
            ),
            'file.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.file')]
            ),
            'file.file' => trans(
                'validation.file',
                ['field' => trans('fields.file')]
            ),
        ];
    }
}
