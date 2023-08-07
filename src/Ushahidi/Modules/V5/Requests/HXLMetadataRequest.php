<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;

class HXLMetadataRequest extends BaseRequest
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
            "private"=>['required','int'],
            "dataset_title"=>['required','max:255'],
            "license_id"=>['required','int'],
            "organisation_id"=>['required','max:255'],
            "source"=>['required','max:255'],
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.exists' => trans('validation.exists', ['field' => trans('fields.user_id')]),
            
            'private.required' => trans('validation.not_empty', ['field' => trans('fields.private')]),
            'private.int' => trans('validation.int', ['field' => trans('fields.private')]),

            'dataset_title.required' => trans('validation.not_empty', ['field' => trans('fields.dataset_title')]),
            'dataset_title.max' => trans('validation.max', ['field' => trans('fields.dataset_title')]),

            'license_id.required' => trans('validation.not_empty', ['field' => trans('fields.license_id')]),
            'license_id.int' => trans('validation.int', ['field' => trans('fields.int')]),

            'organisation_id.required' => trans('validation.not_empty', ['field' => trans('fields.organisation_id')]),
            'organisation_id.max' => trans('validation.max', ['field' => trans('fields.organisation_id')]),

            'source.required' => trans('validation.not_empty', ['field' => trans('fields.source')]),
            'source.max' => trans('validation.max', ['field' => trans('fields.source')]),

        ];
    }
}
