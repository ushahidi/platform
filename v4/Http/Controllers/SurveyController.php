<?php

namespace v4\Http\Controllers;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Resources\Json\Resource;
use Ramsey\Uuid\Uuid;
use Ushahidi\App\Validator\LegacyValidator;
use v4\Http\Resources\SurveyCollection;
use v4\Http\Resources\SurveyResource;
use v4\Models\Attribute;
use v4\Models\Survey;
use Illuminate\Http\Request;
use v4\Models\Translation;


class SurveyController extends V4Controller
{

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return SurveyResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $survey = Survey::with('translations')->find($id);
        if (!$survey) {
            abort(404);
        }
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     * @TODO add enabled_languages (the ones that we have translations for)
     * @return SurveyCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        return new SurveyCollection(Survey::all());
    }

    /**
     * Display the specified resource.
     * @TODO add enabled_languages (the ones that we have translations for)
     * @TODO transactions =)
     * @param Request $request
     * @return SurveyResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request) {
        $authorizer = service('authorizer.form');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        if ($user) {
            $this->authorize('store', Survey::class);
        }
        $this->getValidationFactory()->make($request->input(), Survey::getRules());
        $survey = Survey::create(
            array_merge(
                $request->input(),[ 'updated' => time(), 'created' => time()]
            )
        );
        $this->saveTranslations($request->input('translations'), $survey->id, 'survey');
        if ($request->input('tasks')) {
            foreach ($request->input('tasks') as $stage) {
                $stage_model = $survey->tasks()->create(
                    array_merge(
                        $stage, [ 'updated' => time(), 'created' => time()]
                    )
                );
                $this->saveTranslations($stage['translations'] ?? [], $stage_model->id, 'task');
                foreach ($stage['fields'] as $attribute) {
                    $uuid = Uuid::uuid4();
                    $attribute['key'] = $uuid->toString();
                    $field_model = $stage_model->fields()->create(
                        array_merge(
                            $attribute, [ 'updated' => time(), 'created' => time()]
                        )
                    );
                    $this->saveTranslations($attribute['translations'] ?? [], $field_model->id, 'field');
                }
            }
        }
        return new SurveyResource($survey);
    }

    /**
     * @param $input
     * @param $translatable_id
     * @param $type
     * @return bool
     */
    private function saveTranslations($input, int $translatable_id, string $type) {
        if (!is_array($input)) {
            return true;
        }
        foreach ($input as $language => $translations) {
            foreach ($translations as $key => $translated) {
                if (is_array($translated)){
                    $translated = json_encode($translated);
                }
                Translation::create([
                    'translatable_type' => $type,
                    'translatable_id' => $translatable_id,
                    'translated_key' => $key,
                    'translation' => $translated,
                    'language' => $language
                ]);
            }
        }
    }
    /**
     * Display the specified resource.
     * @TODO add enabled_languages (the ones that we have translations for)
     * @TODO transactions =)
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, Request $request) {
        $survey = Survey::find($id);
        $this->authorize('update', $survey);
        $this->getValidationFactory()->make($request->input(), Survey::getRules());
        $survey = Survey::create(
            array_merge(
                $request->input(),[ 'updated' => time(), 'created' => time()]
            )
        );
        if ($request->input('tasks')) {
            foreach ($request->input('tasks') as $stage) {
                $stage_model = $survey->tasks()->create(
                    array_merge(
                        $stage, [ 'updated' => time(), 'created' => time()]
                    )
                );
                foreach ($stage['fields'] as $attribute) {
                    $uuid = Uuid::uuid4();
                    $attribute['key'] = $uuid->toString();
                    $stage_model->fields()->create(
                        array_merge(
                            $attribute, [ 'updated' => time(), 'created' => time()]
                        )
                    );
                }
            }
        }
        return response()->json(['result' => $survey->load('tasks')]);
    }
}
