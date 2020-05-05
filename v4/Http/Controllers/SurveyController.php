<?php

namespace v4\Http\Controllers;
use Ramsey\Uuid\Uuid;
use Ushahidi\App\Validator\LegacyValidator;
use v4\Models\Attribute;
use v4\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class SurveyController extends V4Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $survey = Survey::find($id);
        $this->authorize('show', $survey);
        return response()->json(['survey' => $survey]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('index', Survey::class);
        return response()->json(['results' => Survey::all()]);
    }

    public function store(Request $request) {
        $validator = $this->getValidationFactory()->make($request->input(), [
            'name' => [
                'required',
                'min:2',
                'max:255',
                'regex:' . LegacyValidator::REGEX_STANDARD_TEXT
            ],
            'description' => [
                'string',
                'nullable'
            ],
            //@TODO find out where this color validator is implemented
            //[['color']],
            'color' => [
                'string',
                'nullable'
            ],
            'disabled' => [
                'boolean'
            ],
            'hide_author' => [
                'boolean'
            ],
            'hide_location' => [
                'boolean'
            ],
            'hide_time' => [
                'boolean'
            ],
            // @FIXME: disabled targeted survey creation for v4 forms, need to check
            'targeted_survey' => [
                Rule::in([false]),
            ],
            'stages.*.label' => [
                'required',
                'regex:' . LegacyValidator::REGEX_STANDARD_TEXT
            ],
            'stages.*.type' => [
                Rule::in(['post', 'task'])
            ],
            'stages.*.priority' => [
                'numeric',
            ],
            'stages.*.icon' => [
                'alpha',
            ],
            'stages.*.attributes.*.label' => [
                'required',
                'max:150'
            ],
            'stages.*.attributes.*.key' => [
                'max:150',
                'alpha_dash'
                // @TODO: add this validation for keys
                //[[$this->repo, 'isKeyAvailable'], [':value']]
            ],
            'stages.*.attributes.*.input' => [
                'required',
                Rule::in([
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
                ])
            ],
            'stages.*.attributes.*.type' => [
                'required',
                Rule::in([
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
                ])
                // @TODO: add this validation for duplicates in type?
                //[[$this, 'checkForDuplicates'], [':validation', ':value']],
            ],
            'stages.*.attributes.*.type' => [
                'boolean'
            ],
            'stages.*.attributes.*.priority' => [
                'numeric',
            ],
            'stages.*.attributes.*.cardinality' => [
                'numeric',
            ],
            'stages.*.attributes.*.response_private' => [
                'boolean'
                // @TODO add this custom validator for canMakePrivate
                // [[$this, 'canMakePrivate'], [':value', $type]]
            ]
            // @NOTE: checkPostTypeLimit is not used here.
            // Before merge, validate with Angela if we
            // should be removing that arbitrary limit since it's pretty rare
            // for it to be needed
        ]);
        $survey = Survey::create(
            array_merge(
                $request->input(),[ 'updated' => time(), 'created' => time()]
            )
        );
        foreach ($request->input('stages') as $stage) {
            $stage_model = $survey->stages()->create(
                array_merge(
                    $stage, [ 'updated' => time(), 'created' => time()]
                )
            );
            foreach ($stage['attributes'] as $attribute) {
                $uuid = Uuid::uuid4();
                $attribute['key'] = $uuid->toString();
                $stage_model->attributes()->create(
                    array_merge(
                        $attribute, [ 'updated' => time(), 'created' => time()]
                    )
                );
            }
        }
        return response()->json(['survey' => $survey->load('stages')]);
    }
}
