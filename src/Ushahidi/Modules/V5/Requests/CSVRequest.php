<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\CSV;
use Illuminate\Http\Request;

class CSVRequest extends BaseRequest
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
    
    public function getMaxBytes()
    {
        // To Do: make it config
        // David Losada update 2024-03-15: originally 20KB, that size not useful to anyone.
        //    Setting to 20MiB, instead.
        //    This very much needs to become a config setting.
        return 20 * 1024 * 1024;
    }

    private function storeRules(Request $request): array
    {
        return [
            'form_id' => 'required|integer|exists:forms,id',
            'file' => 'required|file',
        ];
    }
    
    private function updateRules(Request $request): array
    {
        return [
            'form_id' => 'filled|integer|exists:forms,id',
            'size' => 'filled|integer|min:1|max:'.$this->getMaxBytes(),
            'filename' => 'filled|string',
            'mime' => ['filled',Rule::in(['text/csv', 'text/plain'])],
        ];
    }


    public function messages(): array
    {
        return [
            'form_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.form_id')]
            ),
            'form_id.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.form_id')]
            ),
            'form_id.integer' => trans(
                'validation.integer',
                ['field' => trans('fields.form_id')]
            ),

            'file.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.file')]
            ),
            'file.file' => trans(
                'validation.file',
                ['field' => trans('fields.file')]
            ),


            'size.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.size')]
            ),
            'size.integer' => trans(
                'validation.integer',
                ['field' => trans('fields.size')]
            ),
            'size.min' => trans(
                'validation.min',
                ['field' => trans('fields.size')]
            ),
            'size.max' => trans(
                'validation.max',
                ['field' => trans('fields.size')]
            ),

            'filename.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.filename')]
            ),
            'filename.string' => trans(
                'validation.string',
                ['field' => trans('fields.filename')]
            ),

            'mime.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.mime')]
            ),
            'mime.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.mime')]
            ),

        ];
    }
}
