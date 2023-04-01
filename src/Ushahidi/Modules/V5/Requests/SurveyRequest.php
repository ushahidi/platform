<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;
use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Arr;

class SurveyRequest extends BaseRequest
{
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
    public function rules(Request $request)
    {
        if ($request->isMethod('post')) {
            return $this->postMethodRules();
        } elseif ($request->isMethod('put')) {
            return $this->postMethodRules();
        } else {
            return [];
        }
    }

    public function messages()
    {
        return [
            'name.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.name')]
            ),
            'name.min' => trans(
                'validation.min_length',
                [
                    'param2' => 2,
                    'field' => trans('fields.name'),
                ]
            ),
            'name.max' => trans(
                'validation.max_length',
                [
                    'param2' => 255,
                    'field' => trans('fields.name'),
                ]
            ),
            // 'name.regex' => trans(
            //     'validation.regex',
            //     ['field' => trans('fields.name')]
            // ),
            // @TODO Add description.string
            // @TODO Add color.string
            'disabled.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.disabled')]
            ),
            'everyone_can_create.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.everyone_can_create')]
            ),
            'hide_author.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.hide_author')]
            ),
            'hide_location.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.hide_location')]
            ),
            'hide_time.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.hide_time')]
            ),
            'targeted_survey.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.targeted_survey')]
            ),
            'tasks.*.label.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.tasks.label')]
            ),
            'tasks.*.label.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.tasks.label')]
            ),
            'tasks.*.type.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.tasks.type')]
            ),
            'tasks.*.priority.numeric' => trans(
                'validation.numeric',
                ['field' => trans('fields.tasks.priority')]
            ),
            'tasks.*.icon.alpha' => trans(
                'validation.alpha',
                ['field' => trans('fields.tasks.icon')]
            ),
            'tasks.*.fields.*.label.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.tasks.fields.label')]
            ),
            'tasks.*.fields.*.label.max' => trans(
                'validation.max_length',
                [
                    'param2' => 150,
                    'field' => trans('fields.tasks.fields.label'),
                ]
            ),
            'tasks.*.fields.*.key.alpha_dash' => trans(
                'validation.alpha_dash',
                ['field' => trans('fields.tasks.fields.key')]
            ),
            'tasks.*.fields.*.key.max' => trans(
                'validation.max_length',
                [
                    'param2' => 150,
                    'field' => trans('fields.tasks.fields.key'),
                ]
            ),
            'tasks.*.fields.*.input.required' => trans(
                'validation.not_empty',
                ['param2' => trans('fields.tasks.fields.input')]
            ),
            'tasks.*.fields.*.input.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.tasks.fields.input')]
            ),
            'tasks.*.fields.*.type.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.tasks.fields.type')]
            ),
            'tasks.*.fields.*.type.in' => trans(
                'validation.in_array',
                ['field' => trans('fields.tasks.fields.type')]
            ),
            'tasks.*.fields.*.priority.numeric' => trans(
                'validation.numeric',
                ['field' => trans('fields.tasks.fields.priority')]
            ),
            'tasks.*.fields.*.cardinality.numeric' => trans(
                'validation.numeric',
                ['field' => trans('fields.tasks.fields.cardinality')]
            ),
            'tasks.*.fields.*.response_private.boolean' => trans(
                'validation.regex',
                ['field' => trans('fields.tasks.fields.response_private')]
            ),
            // 'tasks.*.fields.*.response_private' => [
            // @TODO add this custom validator for canMakePrivate
            // [[$this, 'canMakePrivate'], [':value', $type]]
            // ]
        ];
    }
  
    private function postMethodRules()
    {
        return [
            'name' => [
                'required',
                'min:2',
                'max:255',
                'regex:' . LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'description' => [
                'string',
                'nullable',
            ],
            'color' => [
                'string',
                'nullable',
            ],
            'disabled' => ['boolean'],
            'everyone_can_create' => ['boolean'],
            'hide_author' => ['boolean'],
            'hide_location' => ['boolean'],
            'hide_time' => ['boolean'],
            'targeted_survey' => ['boolean'],
            'tasks.*.label' => [
                'required',
                'regex:' . LegacyValidator::REGEX_STANDARD_TEXT,
            ],
            'tasks.*.type' => [
                Rule::in(
                    [
                        'post',
                        'task',
                    ]
                )
            ],
            'tasks.*.priority' => ['numeric'],
            'tasks.*.icon' => ['alpha'],
            'tasks.*.fields.*.label' => [
                'required',
                'max:150',
            ],
            'tasks.*.fields.*.key' => [
                'max:150',
                'alpha_dash',
                // @TODO: add this validation for keys
                // [[$this->repo, 'isKeyAvailable'], [':value']]
            ],
            'tasks.*.fields.*.input' => [
                'required',
                Rule::in(
                    [
                        'text',
                        'textarea',
                        'select',
                        'radio',
                        'checkbox',
                        'checkboxes',
                        'date',
                        'datetime',
                        'location',
                        'number',
                        'relation',
                        'upload',
                        'video',
                        'markdown',
                        'tags',
                    ]
                ),
            ],
            'tasks.*.fields.*.type' => [
                'required',
                Rule::in(
                    [
                        'decimal',
                        'int',
                        'geometry',
                        'text',
                        'varchar',
                        'markdown',
                        'point',
                        'datetime',
                        'link',
                        'relation',
                        'media',
                        'title',
                        'description',
                        'tags',
                    ]
                )
            ],
            'tasks.*.fields.*.type' => ['string'],
            'tasks.*.fields.*.priority' => ['numeric'],
            'tasks.*.fields.*.cardinality' => ['numeric'],
            'tasks.*.fields.*.response_private' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    $type_field = Arr::get(RequestFacade::input(), str_replace('response_private', 'type', $attribute));
                    if ($type_field === 'tags' && $value != false) {
                        return $fail(trans('validation.tag_field_type_cannot_be_private'));
                    }
                }
            ]
        ];
    }
}
