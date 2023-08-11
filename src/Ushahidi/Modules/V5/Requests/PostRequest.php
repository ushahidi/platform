<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Ushahidi\Modules\V5\Models\Post;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Rules\StandardText;
use Ushahidi\Modules\V5\Models\Post\PostStatus;
use Illuminate\Support\Facades\Request as RequestFacade;

class PostRequest extends BaseRequest
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
           // dd($this->storeRules());

          //  dd($this->updateRules());
            return $this->updateRules();
        } else {
            return [];
        }
    }
    
    public function storeRules(): array
    {
        return [
            'form_id' => 'required|sometimes|exists:forms,id',
            'user_id' => 'nullable|sometimes|exists:users,id',
            'type' => [
                'required',
                Rule::in(['report','update','revision'])
            ],
            'title' => [
                'required',
                'max:150',
                //new StandardText,
            ],
            'slug' => [
                'nullable',
                'min:2',
                Rule::unique('posts')->ignore($this->id)
            ],
            'content' => [
                'string'
            ],
            'author_email' => 'nullable|sometimes|email|max:150',
            'author_realname' => 'nullable|sometimes|max:150',
            'status' => [
                'filled',
                Rule::in(PostStatus::all())
            ],
            'post_content.*.form_id' => [
                'same:form_id'
            ],
            'post_content.*.fields' => [
                'present'
            ],
            'post_content.*.fields.*.required' => [
                function ($attribute, $value, $fail) {
                    if (!!$value) {
                        $field_content = RequestFacade::input(str_replace('.required', '', $attribute));
                        $label = $field_content['label'] ?: $field_content['id'];
                        $get_value = RequestFacade::input(str_replace('.required', '.value.value', $attribute));
                        $is_empty = (is_null($get_value) || $get_value === '');
                        $is_title = RequestFacade::input(str_replace('.required', '.type', $attribute)) === 'title';
                        $is_desc = RequestFacade::input(
                            str_replace('.required', '.type', $attribute)
                        ) === 'description';
                        if ($is_empty && !$is_desc && !$is_title) {
                            return $fail(
                                trans('validation.required_by_label', [
                                    'label' => $label
                                ])
                            );
                        }
                    }
                }
            ],
            'post_content.*.fields.*.type' => [
                function ($attribute, $value, $fail) {
                    $get_value = RequestFacade::input(str_replace('.type', '.value.value', $attribute));
                    if ($value === 'tags' && !is_array($get_value)) {
                        return $fail(trans('validation.tag_field_must_be_array'));
                    }
                }
            ],
            'locale',
            'post_date'
        ];
    }

    public function updateRules(): array
    {
        $rules = $this->storeRules();
        // change reuired to filled in update
        $rules['type'][0] = "filled";
        $rules['form_id'] = "filled|sometimes|exists:forms,id";
        $rules['title'][0] = "filled";
        $rules['status'][0] = "filled";
        unset($rules['post_content.*.fields.*.required']);
        //unset($rules['post_content.*.fields.*.type']);
        return $rules;
    }
    public function messages(): array
    {
        return [
            'form_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.form_id')]
            ),
            'user_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.user_id')]
            ),
            'type.required' => trans(
                'validation.required',
                ['field' => trans('fields.type')]
            ),
            'type.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.type')]
            ),
            'title.required' => trans(
                'validation.required',
                ['field' => trans('fields.title')]
            ),
            'title.max' => trans(
                'validation.max',
                [
                    'param2' => 150,
                    'field' => trans('fields.title'),
                ]
            ),
            'title.regex' => trans(
                'validation.regex',
                [
                    'field' => trans('fields.title'),
                ]
            ),
            'slug.required' => trans(
                'validation.required',
                ['field' => trans('fields.slug')]
            ),
            'slug.min' => trans(
                'validation.min',
                [
                    'param2' => 2,
                    'field' => trans('fields.slug'),
                ]
            ),
            'slug.unique' => trans(
                'validation.unique',
                [
                    'field' => trans('fields.slug'),
                ]
            ),
            'content.string' => trans(
                'validation.string',
                [
                    'field' => trans('fields.content'),
                ]
            )
        ];
    }
}
