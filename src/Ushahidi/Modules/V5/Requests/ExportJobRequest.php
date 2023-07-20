<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Notification;
use Illuminate\Http\Request;
use Ushahidi\Core\Facade\Feature;

class ExportJobRequest extends BaseRequest
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
        // To Do : add hxl columns validation
        return [
            'entity_type' => ['required',Rule::in(['post'])]
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
            'entity_type' => ['filled',Rule::in(['post'])]
        ];
    }


    public function messages(): array
    {
        return [
            'entity_type.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.entity_type')]
            ),
            'entity_type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.entity_type')]
            ),
        ];
    }
}
