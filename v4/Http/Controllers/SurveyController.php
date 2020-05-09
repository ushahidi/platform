<?php

namespace v4\Http\Controllers;
use Illuminate\Http\Resources\Json\Resource;
use Ramsey\Uuid\Uuid;
use Ushahidi\App\Validator\LegacyValidator;
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
        return new \v4\Http\Resources\SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     * @TODO add enabled_languages (the ones that we have translations for)
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('index', Survey::class);
        return new \v4\Http\Resources\SurveyCollection(Survey::all());
    }

    /**
     * Display the specified resource.
     * @TODO add enabled_languages (the ones that we have translations for)
     * @TODO transactions =)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request) {
        $this->authorize('store', Survey::class);
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
        return new \v4\Http\Resources\SurveyResource($survey);
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
