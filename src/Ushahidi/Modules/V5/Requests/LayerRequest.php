<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Layer;
use Illuminate\Http\Request;

class LayerRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {
        $layer_id = $request->route('id') ? $request->route('id') : null;

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
            'name' => ['required', 'min:2', 'max:50'],
            'type' => ['required', Rule::in(['geojson', 'wms', 'tile'])],
            'active' => ['required', 'boolean'],
            'visible_by_default' => ['required', 'boolean'],
            'media_id' => 'required_without_all:data_url|exists:media,id',
            'data_url'=>'required_without_all:media_id'
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
            'name' => ['filled', 'min:2', 'max:50'],
            'type' => ['filled', Rule::in(['geojson', 'wms', 'tile'])],
            'active' => ['filled', 'boolean'],
            'visible_by_default' => ['filled', 'boolean'],
            'media_id' => 'required_without_all:data_url|exists:media,id',
            'data_url'=>'required_without_all:media_id'
        ];
    }


    public function messages(): array
    {
        return [

            'name.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),

            'name.min' => trans(
                'validation.min_length',
                [
                    'param2' => 3,
                    'field' => trans('fields.name'),
                ]
            ),

            'name.max' => trans(
                'validation.max_length',
                [
                    'param2' => 50,
                    'field' => trans('fields.name'),
                ]
            ),

            'type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.type')]
            ),
            'type.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.type')]
            ),
            'active.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.active')]
            ),
            'active.required' => trans(
                'validation.boolean',
                ['field' => trans('fields.active')]
            ),

            'visible_by_default.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.visible_by_default')]
            ),
            'visible_by_default.required' => trans(
                'validation.boolean',
                ['field' => trans('fields.visible_by_default')]
            ),
            'media_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.media_id')]
            )
        ];
    }
}
