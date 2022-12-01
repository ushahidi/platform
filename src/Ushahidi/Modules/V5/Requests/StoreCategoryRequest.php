<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Category;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
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
                'required',
                'min:2',
                Rule::unique('tags', 'slug')
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
            'role' => [
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
}
