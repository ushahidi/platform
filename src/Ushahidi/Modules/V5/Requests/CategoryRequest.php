<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Category;
use Illuminate\Http\Request;

class CategoryRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {
        $category_id= $request->route('id')?$request->route('id'):null;

        if ($request->isMethod('post')) {
            return $this->storeRules();
        } elseif ($request->isMethod('put')) {
            $rules = $this->storeRules();
            $rules['tag'] = [
                'filled',
                'min:2',
                'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD',
                'unique:tags,tag,'.$category_id
            ];
            $rules['type'] = ['filled',Rule::in(['category','status'])];
            return $rules;
        } else {
            return [];
        }
    }
    
    private function storeRules(): array
    {
        $parentId = $this->input('parent_id');
        return [
            'parent_id' => 'nullable|sometimes|exists:tags,id',
            'tag' => [
                'required',
                'min:2',
                'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD',
                Rule::unique('tags', 'tag')
            ],
            'slug' => [
                'nullable',
                'min:2',
            ],
            'type' => [
                'required',
                Rule::in([
                    'category',
                    'status'
                ])
            ],
            'description' => [
                'nullable',
                'min:2',
                'max:255'
            ],
            'color' => [
                'string',
                'nullable',
            ],
            'icon' => [
                'regex:/^[\pL\s\_\-]++$/uD'
            ],
            'priority' => [
                'numeric'
            ],
            'role' => ["array",
                function ($attribute, $value, $fail) use ($parentId) {
                    $parent = $parentId ? Category::find($parentId) : null;
                    // ... and check if the role matches its parent
                    if ($parent && $parent->role != $value) {
                        return $fail(trans('validation.child_parent_role_match'));
                    }
                    if (is_array($value) && empty($value)) {
                        return $fail(trans('validation.role_cannot_be_empty'));
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.parent_id')]
            ),
            'tag.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.tag')]
            ),
            'tag.unique' => trans(
                'validation.unique',
                ['field' => trans('fields.tag')]
            ),
            'tag.min' => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field' => trans('fields.tag'),
                ]
            ),
            'tag.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.tag'),
                ]
            ),
            'tag.regex' => trans(
                'validation.regex',
                ['field' => trans('fields.tag')]
            ),
            'slug.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.slug')]
            ),
            'slug.min' => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field' => trans('fields.slug'),
                ]
            ),
            'slug.unique' => trans(
                'validation.unique',
                ['field' => trans('fields.slug')]
            ),
            'type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.type')]
            ),
            'type.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.type')]
            ),
            'description.regex' => trans(
                'validation.regex',
                ['field' => trans('fields.description')]
            ),

            'description.min' => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field' => trans('fields.description')
                ]
            ),

            'description.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.description')
                ]
            ),
            'icon.regex' => trans(
                'validation.regex',
                ['field' => trans('fields.icon')]
            ),
            'priority.numeric' => trans(
                'validation.numeric',
                ['field' => trans('fields.priority')]
            )
        ];
    }

    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([
    //         'errors' => $validator->errors()
    //     ], 422));
    // }

    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json(['messages' => $validator->errors()], 422));
    // }
}
