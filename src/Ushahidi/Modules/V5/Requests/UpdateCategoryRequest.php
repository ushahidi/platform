<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Category;

class UpdateCategoryRequest extends FormRequest
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
                'nullable',
                'min:2',
                'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD',
            ],
            'slug' => [
                'nullable',
                'min:2',
            ],
            'type' => [
                'nullable',
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

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
