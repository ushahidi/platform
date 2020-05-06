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
    protected static function getRules() {
        return [
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
        ];
    }
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $survey = Survey::with('translations')->find($id);
        $not_found = !$survey;
        if ($not_found) {
            $survey = new Survey();
        }
        // we try to authorize even if we don't find a survey
        // this allows us to return a 404 to users who would
        // be allowed to read surveys and a 403 to those who wouldn't
        // obfuscating the existence of particular unauthorized surveys
        // or non-existent ones to users without any permissions to see them
        $this->authorize('show', $survey);
        if ($not_found) {
            abort(404);
        }
        return response()->json(['result' => $survey]);
    }

    /**
     * Display the specified resource.
     * @TODO add translation keys to each object =)
     * @TODO add enabled_languages (the ones that we have translations for)
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('index', Survey::class);
        return response()->json(['results' => Survey::all()]);
    }

    /**
     * Display the specified resource.
     * @TODO add translation keys to each object =)
     * @TODO add enabled_languages (the ones that we have translations for)
     * @TODO transactions =)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request) {
        $this->authorize('store', Survey::class);
        $this->getValidationFactory()->make($request->input(), self::getRules());
        $survey = Survey::create(
            array_merge(
                $request->input(),[ 'updated' => time(), 'created' => time()]
            )
        );
        if ($request->input('stages')) {
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
        }
        return response()->json(['result' => $survey->load('stages')]);
    }

    /**
     * Display the specified resource.
     * @TODO add translation keys to each object =)
     * @TODO add enabled_languages (the ones that we have translations for)
     * @TODO transactions =)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, Request $request) {
        $survey = Survey::find($id);
        $this->authorize('update', $survey);
        $this->getValidationFactory()->make($request->input(), self::getRules());
        $survey = Survey::create(
            array_merge(
                $request->input(),[ 'updated' => time(), 'created' => time()]
            )
        );
        if ($request->input('stages')) {
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
        }
        return response()->json(['result' => $survey->load('stages')]);
    }
}
