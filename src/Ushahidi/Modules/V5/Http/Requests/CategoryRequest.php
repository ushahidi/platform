<?php

namespace Ushahidi\Modules\V5\Http\Requests;

use Ushahidi\Modules\V5\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * @var \Ushahidi\Modules\V5\Models\Category
     */
    public $category;

    protected $id;

    protected $parent;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules($data = [])
    {
        $id = $data['id'] ?? $this->id;
        $parent = $data['parent'] ?? $this->parent;

        return [
            'parent_id' => 'nullable|sometimes|exists:tags,id',
            'tag' => [
                'required', 'min:2', 'max:255',
                'regex:/^[\pL\pN\pP ]++$/uD',
                Rule::unique('tags', 'tag')->ignore($id)
            ],
            'type' => ['required', Rule::in(['category', 'status'])],
            'description' => ['nullable', 'min:2', 'max:255'],
            'color' => ['string', 'nullable'],
            'icon' => ['regex:/^[\pL\s\_\-]++$/uD'],
            'priority' => ['numeric'],
            'role' => [
                function ($attribute, $value, $fail) use ($parent) {
                    // ... and check if the role matches its parent
                    if ($parent && $parent->getAttribute('role') != $value) {
                        return $fail(trans('validation.child_parent_role_match'));
                    }
                    if (is_array($value) && empty($value)) {
                        return $fail(trans('validation.role_cannot_be_empty'));
                    }
                }
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
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
            ),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->sometimes('role', 'exists:roles,name', function ($input) {
            return !!$input->get('role');
        });
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case 'store':
                $this->merge([
                    'slug' => Category::makeSlug($this->input('slug') ?? $this->input('tag'))
                ]);
                break;
            case 'update':
                $this->category = Category::withoutGlobalScopes()->find($this->route('id'));
                if (!$this->category) {
                    throw new HttpResponseException(
                        response()->json([
                            'error' => 404,
                            'messages' => 'Not found',
                        ], JsonResponse::HTTP_NOT_FOUND)
                    );
                }
                break;
            default:
                break;
        }
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        if ($this->exists('parent_id')) {
            $this->parent = Category::find($this->input('parent_id'));
        } elseif ($this->exists('id')) {
            $this->parent = Category::find($this->input('id'))->parent;
        } elseif (isset($this->category->parent)) {
            $this->parent = $this->category->parent;
        } else {
            $this->parent = null;
        }

        $this->id = $this->input('id', $this->category->id ?? null);

        return parent::validationData();
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\UnauthorizedException
     */
    protected function failedAuthorization()
    {
        parent::failedAuthorization();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        try {
            parent::failedValidation($validator);
        } catch (ValidationException $e) {
            $errors = $e->errors();

            throw new HttpResponseException(
                response()->json([
                    'error' => 422,
                    'messages' => $errors,
                    'type' => 'category'
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
